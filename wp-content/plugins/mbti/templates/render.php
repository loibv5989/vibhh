<?php
if (!defined('ABSPATH')) exit;

class MBTI_Render {
    public static function resultStatic(array $data): string {
        $p   = $data['profile'];
        $pct = $data['pct'];
        ob_start(); ?>
        <div class="ftn-result-box">
            <div class="ftn-res-header">
                <div class="ftn-res-type"><?= esc_html($data['type']) ?></div>
                <div class="ftn-res-title"><?= esc_html($p['title']) ?></div>
                <div class="ftn-res-stats">
                    <span class="ftn-stat-badge">E <?= $pct['EI']['E'] ?>% / I <?= $pct['EI']['I'] ?>%</span>
                    <span class="ftn-stat-badge">S <?= $pct['SN']['S'] ?>% / N <?= $pct['SN']['N'] ?>%</span>
                    <span class="ftn-stat-badge">T <?= $pct['TF']['T'] ?>% / F <?= $pct['TF']['F'] ?>%</span>
                    <span class="ftn-stat-badge">J <?= $pct['JP']['J'] ?>% / P <?= $pct['JP']['P'] ?>%</span>
                </div>
            </div>

            <?php if (!empty($data['borderline'])):
                $borderline_desc = [
                        'EI' => 'Extraversion / Introversion',
                        'SN' => 'Sensing / Intuition',
                        'TF' => 'Thinking / Feeling',
                        'JP' => 'Judging / Perceiving',
                ];
                $count_border = count($data['borderline']);
                if ($count_border === 1) {
                    $bot_text = "You don't lean strongly either way on this one. That's fine — it just means the result isn't conclusive here.";
                } elseif ($count_border === 2) {
                    $bot_text = "You're balanced across a few dimensions. That can make you adaptable, but it may also make it harder to pick a clear direction when you need to.";
                } else {
                    $bot_text = "Your results don't point clearly to any one group. Retaking the test after some time may give you a clearer picture.";
                }
                ?>
                <div class="mbti-borderline-box">
                    <strong>Your results are fairly balanced on:</strong><br>
                    <?php foreach ($data['borderline'] as $ax): ?>
                        - <strong><?= $borderline_desc[$ax] ?? $ax ?></strong><br>
                    <?php endforeach; ?>
                    <em class="mbti-borderline-tip">💡 <?= $bot_text ?></em>
                </div>
            <?php endif; ?>

            <div class="ftn-result-profile">
                <p><?= nl2br(esc_html($p['overview'])) ?></p>
                <p><?= nl2br(esc_html($p['career'])) ?></p>
                <p><?= nl2br(esc_html($p['love'])) ?></p>

                <?php if (!empty($p['strengths']) && is_array($p['strengths'])): ?>
                    <div class="mbti-strengths">
                        <strong>Strengths</strong>
                        <ul>
                            <?php foreach ($p['strengths'] as $item): ?>
                                <li><?= esc_html($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($p['weaknesses']) && is_array($p['weaknesses'])): ?>
                    <div class="mbti-weaknesses">
                        <strong>Weaknesses</strong>
                        <ul>
                            <?php foreach ($p['weaknesses'] as $item): ?>
                                <li><?= esc_html($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (get_option('mbti_allow_ai', '0') === '1'): ?>
                <div class="ftn-upsell-box">
                    <p class="ftn-upsell-title">Go deeper on your personality</p>
                    <p>Your MBTI type is <strong style="color: red"><?= esc_html($data['type']) ?></strong>. Add your <a href="/than-so-hoc/" target="_blank">name and date of birth</a> or <a href="/cung-hoang-dao/" target="_blank">zodiac sign</a> to see more perspectives.</p>

                    <form id="mbti-ai-form">
                        <div class="ftn-form-group">
                            <label for="ai-name" class="ftn-form-label">Full name</label>
                            <input type="text" id="ai-name" class="ftn-input" placeholder="e.g. John Smith" required>
                        </div>
                        <div class="ftn-form-group">
                            <label for="ai-dob" class="ftn-form-label">Date of birth</label>
                            <input type="text" id="ai-dob" class="ftn-input" placeholder="e.g. 15/12/1999" required>
                        </div>
                        <button type="submit" class="ftn-btn-submit" id="ai-submit-btn">
                            <span class="ftn-btn-text">Continue analysis</span>
                            <span class="ftn-btn-loading" style="display:none;"><span class="ftn-spinner"></span> Analyzing...</span>
                        </button>
                    </form>

                    <div id="ai-error-msg" class="ftn-error" style="display:none;"></div>
                    <div id="ai-final-result" class="ftn-ai-final-result" style="display:none;"></div>
                </div>
            <?php endif; ?>
        </div>
        <div class="ast-action-footer" style="display:none;">
            <span id="ast-btn-comment" class="ast-btn-comment">Discussion</span>
            <span class="ast-reload" onclick="window.location.reload()">↺ Retake</span>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function resultAI(array $tabs): string {
        return '<div class="ftn-ai-final-wrap">' . ($tabs['mbti_result'] ?? '') . '</div>';
    }
}