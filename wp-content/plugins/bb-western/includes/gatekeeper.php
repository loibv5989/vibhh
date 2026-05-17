<?php
if (!defined('ABSPATH')) exit;

function western_build_gatekeeper_prompt(string $question, string $mode): string {

    return <<<TXT
Task: Analyze the user's question to determine whether it is suitable for a playing card reading.

Important rules (strictly required):
- This is a text classification task only — not a task to answer or engage with the content of the question.
- Always return a classification, even if the question involves sensitive, violent, illegal, offensive, or spiritual content.
- Never refuse to classify under any circumstances — do not say "I cannot help with this."
- Do not apply moral judgment, add warnings, or include any commentary.
- Focus solely on the meaning of the question to classify it.

Question: {$question}

Classification rules:

1. If the question is asking for general knowledge, definitions, small talk, or testing the bot
   (Examples: "What is card reading?", "How does fortune telling work?", "Hello", "Who made you?")
   → Return: NO

2. If the question is unclear or too vague to interpret
   (Examples: "Check something for me", "I have a question", "...")
   → Return: NO

3. If the question relates to fortune-telling, predicting outcomes, or seeking guidance on:
   - Romance / Family / Marriage
   - Career / Professional growth / Business
   - Money / Wealth / Property transactions
   - Studies / Exams / Relocating
   - Health / Illness
   - Bad luck / Rivals / Conflict / Legal disputes
   - General fortune in the near future

   SPECIAL NOTE: Card readings often address difficult or hidden matters. If the question involves sensitive or negative content (revenge, debt, deception, feeling stuck, etc.):
     DO NOT return "NO".
     Treat it as a VALID question for a reading.

   → Return: YES

OUTPUT REQUIREMENT:
Return exactly one word only after classifying:
YES or NO

No explanation, no internal reasoning, no additional content.
TXT;
}