<?php
// FILE: render.php
if (!defined('ABSPATH')) exit;

class BS_Phone_Render {

    public static function indexes(array $data, string $block4 = ''): string {
        $chat_lines = BS_Phone_Calc::get_chat_lines($data);

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="ftn-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="chi-tiet"  role="tab">Kết Quả</button>
                <button class="ftn-tab" data-tab="phan-tich" role="tab">Chi Tiết</button>
            </div>
            <div class="ftn-tab-pane active" id="ftn-tab-chi-tiet">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-body" id="ftn-chat-body"
                         data-lines="<?= esc_attr(json_encode($chat_lines, JSON_UNESCAPED_UNICODE)) ?>">
                        <span class="ftn-cursor">|</span>
                    </div>
                </div>
            </div>
            <div class="ftn-tab-pane" id="ftn-tab-phan-tich"></div>
        </div>

        <?php
        return ob_get_clean();
    }

    public static function narrative_tabs(array $narrative): string {
        ob_start(); ?>
        <div id="ftn-response-static-data" style="display:none;">
            <div id="static-tab-phan-tich">
                <div class="phonesync-narrative">
                    <?= wp_kses_post($narrative['block1'] ?? '') ?>
                    <?= wp_kses_post($narrative['block2'] ?? '') ?>
                    <?= wp_kses_post($narrative['block3'] ?? '') ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
