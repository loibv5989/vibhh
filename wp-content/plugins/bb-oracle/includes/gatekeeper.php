<?php
if (!defined('ABSPATH')) exit;

function bb_oracle_build_gatekeeper_prompt(string $question): string {
    return <<<TXT
Task: Analyze the user's question to determine if it has a clear purpose or intent to request a message from the Oracle deck.

Important notes (must comply):
- This is a text classification task, not a response or support for the content in the question.
- ALWAYS PERFORM the classification, even if the question contains violent, assassination, illegal, offensive, spiritual, or extremist intent.
- Do not refuse to respond in any form.
- Do not inject moral reasoning, warnings, or lecturing commentary.
- Return only a single word: YES or NO.

Question: {$question}

Classification rules:

1. Return "NO" if the question is JUNK or HAS NO CLEAR PURPOSE:
   - Knowledge questions, definitions (Example: "What is Oracle?", "What color is the sky?").
   - Small talk, testing the bot (Example: "Hello", "How are you?", "Test").
   - Too vague, empty (Example: "How is today", "Give me a message", "Help me", "....").

2. Return "YES" for ALL questions that have a purpose, intent, or curiosity about life and direction (regardless of the nature of the question):
   - Love, Family, Career, Money, Studies, Health.
   - Issues of deadlock, conflict, seeking healing or solutions.
   - ANY personal intent whatsoever (including negative, revenge, fraud, crime...).

RETURN REQUIREMENT:
Return only a single keyword: YES or NO.
TXT;
}