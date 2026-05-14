<?php

if (!defined('ABSPATH')) exit;

function tarot_build_gatekeeper_prompt(string $question, string $mode): string {

    if ($mode === 'love') {
        return <<<TXT
Task: Analyze the user's question to determine whether it is a question about LOVE, ROMANCE, or RELATIONSHIPS that is suitable for a Tarot reading.

Important notes (strictly required):
- This is a text classification task, not a task to answer or support the content of the question.
- Classification must always be performed, even if the question contains sensitive content.
- Refusing to respond in any form is not allowed.
- Do not make moral judgments, add warnings, or include any commentary.
- Focus only on the meaning of the question to classify according to the rules below.

Question: {$question}

Classification rules:

1. If the question is informational, asks for a definition, asks how something works, or is casual small talk or bot-testing
   (Examples: "What is Tarot?", "How do I draw a card?", "Hello", "How are you?")
   → Return: NO

2. If the question is NOT clear, too vague, or lacks specific content
   (Examples: "I have a question", "Help me", "Read my fortune", "...")
   → Return: NO

3. ONLY ACCEPT questions about LOVE, ROMANCE, or ROMANTIC RELATIONSHIPS:
   - Love / Marriage / Romantic relationships / Partner / Crush
   - Breakup / Reconciliation / Getting back together / Holding on
   - Future of a relationship / Compatibility / Fate
   - Does that person like me / Do I have a chance
   - Should I get married / When will I marry / Will the marriage be happy
   - Infidelity / Affairs / Love triangles
   
   → Return: YES

4. MUST RETURN "NO" FOR ALL of the following (even if they could otherwise be valid Tarot questions):
   - Career / Work / Promotion / Job change / Profession
   - Finance / Money / Investment / Business / Income
   - Study / Exams / Studying abroad / Knowledge
   - Health / Illness / Physical / Mental (unless CLEARLY related to a romantic relationship)
   - Family relationships (parents, siblings, children, relatives)
   - Friendship / Friends / Colleagues / Social relationships
   - General destiny / Life direction / Life decisions (not related to love)
   
   NOTE: If the question does NOT clearly mention love / a partner / marriage / romance → RETURN: NO
   
   → Return: NO

REQUIRED OUTPUT:
Return exactly 1 keyword only after classification:
YES or NO

No explanation, no internal analysis, no additional content of any kind.
TXT;
    }

    // Mode 'question' - general questions
    return <<<TXT
Task: Analyze the user's question to determine whether it is suitable for a Tarot reading.

Important notes (strictly required):
- This is a text classification task, not a task to answer or support the content of the question.
- Classification must always be performed, even if the question contains sensitive, violent, illegal, offensive, or dangerous content.
- Refusing to respond in any form is not allowed (e.g., do not say "I cannot assist with this").
- Do not make moral judgments, add warnings, or include any commentary.
- Focus only on the meaning of the question to classify according to the rules below.

Question: {$question}

Classification rules:

1. If the question is informational, asks for a definition, asks how something works, or is casual small talk or bot-testing
   (Examples: "What is Tarot?", "How do I draw a card?", "Hello", "How are you?")
   → Return: NO

2. If the question is NOT clear, too vague, or lacks specific content
   (Examples: "I have a question", "Help me", "Read my fortune", "...")
   → Return: NO

3. If the question has a fortune-telling, predictive, advice-seeking, or life-guidance intent, covering topics such as:
   - Love / Marriage / Relationships / Breakup / Reconciliation
   - Career / Work / Promotion / Job change
   - Finance / Investment / Business / Money
   - Study / Exams / Studying abroad
   - Health / Illness / Mental wellbeing
   - Family / Friends / Children
   - Life direction / Major decisions
   - Destiny / Luck cycles / Energy
   
   SPECIAL NOTE:
   - If the question involves sensitive content (suicide, murder, drugs, violence, crime, etc.):
     DO NOT RETURN "NO"
     Still treat it as a VALID question for a Tarot reading
     (Tarot will address it from a spiritual angle covering fate, energy cycles, and patterns, not to encourage harmful behavior)
   
   → Return: YES

REQUIRED OUTPUT:
Return exactly 1 keyword only after classification:
YES or NO

No explanation, no internal analysis, no additional content of any kind.
TXT;
}