---
name: asd-ste100
description: Writes text in ASD-STE100 Simplified Technical English (STE), Issue 9, the controlled language standard for technical documentation. Use it when the user asks for STE, Simplified Technical English, ASD-STE100, or a controlled language, or wants clear technical documentation, procedures, or manuals.
---

# ASD-STE100 Simplified Technical English (Issue 9)

STE is a controlled natural language and an international standard for technical documentation.
Its writing rules (Part 1) have 9 sections that contain 53 rules.
Its dictionary (Part 2) gives 875 approved words and alternatives for words that are not approved.
This skill tells you how to apply the rules and the dictionary when you write.

## When to use

Apply this skill when you write or rewrite technical documentation in STE. STE has two types of writing:

- **Procedural writing (Section 5).** Procedures give instructions that tell the reader how to do a task. Write each instruction in the imperative (command) form.
- **Descriptive writing (Section 6).** Descriptions give information, not instructions. The imperative form is not permitted. Examples: a description of a system, a report, or a note in a procedure.

## How to write STE

1. Identify the type of text: procedural (Section 5) or descriptive (Section 6).
2. Select the words. Obey the word-selection flow in [references/word-selection.md](references/word-selection.md). When you do not know if a word is approved, find it in the dictionary files:
   - Approved words: `grep -i "^ACCESS " references/dictionary-approved.md`
   - Words that are not approved: `grep -i "^accomplish " references/dictionary-unapproved.md`
3. Apply the core limits in the table below.
4. Apply all 53 rules in [references/writing-rules.md](references/writing-rules.md).
5. Write the safety instructions. Obey Section 7 and the "Safety instructions" section below.
6. Do a check of your text with the checklist below.

## Core limits

These limits always apply. The rule numbers refer to Part 1 of the standard.

| Limit | Rule |
| --- | --- |
| Maximum 20 words in each sentence of a procedure (safety instructions included) | 5.1 |
| Maximum 25 words in each sentence of descriptive text | 6.3 |
| Maximum 25 words in each sentence of a note | 5.5 |
| Maximum of six sentences in each paragraph | 6.6 |
| Multi-word nouns of no more than three words | 2.1 |
| Only the approved verb forms and tenses: infinitive, imperative, simple present, simple past, simple future, past participle (as an adjective) | 3.2 |
| Use the active voice. In descriptive writing, the passive voice is permitted only if the agent is unknown | 3.6 |
| Only one instruction in each sentence, unless two or more actions occur at the same time | 5.2 |
| No semicolon (;) | 8.1 |
| In a vertical list, a colon (:) has the same effect on word count as a period | 8.4 |
| Text in parentheses counts as one word in its sentence | 8.5 |
| These count as one word each: numbers, numbers with units, abbreviations, alphanumeric identifiers, quoted text, titles, headings, placard text, and proper nouns | 8.6 |
| Hyphenated words count as one word | 8.7 |
| When applicable, use an article (the, a, an) or a demonstrative adjective (this, these) before a noun or a multi-word noun | 4.5 |

## Safety instructions

A warning tells the reader that there is a risk of injury or death.
A caution tells the reader that there is a risk of damage to objects (Section 7, Definitions).
If the two levels of risk occur together, use a warning (Rule 7.1). A note gives only information, not instructions (Rule 5.5).
Start each safety instruction with a clear and accurate command or condition (Rule 7.2). Then give an explanation to show the risk or possible result (Rule 7.3).

## Check your text

Make sure that your text obeys these rules:

- Each word is approved in the dictionary, a technical noun, or a technical verb (1.1).
- Each approved word keeps its approved part of speech and meaning (1.2, 1.3).
- Verbs use only the approved forms and tenses (3.2). The voice is active (3.6).
- Instructions use the imperative form (5.3). Each sentence has only one instruction (5.2).
- Sentences obey the limits: 20 words in procedures (5.1), 25 words in descriptions (6.3) and notes (5.5).
- Each paragraph has one topic (6.5) and a maximum of six sentences (6.6).
- Each multi-word noun has a maximum of three words (2.1).
- The text has no semicolons (8.1). Word counts obey rules 8.4 thru 8.7.
- Notes give information only, not instructions (5.5).
- Each safety instruction shows the risk level, a command or condition, and an explanation (7.1 thru 7.3).
- Articles and demonstrative adjectives come before nouns, when applicable (4.5).

## References

- [references/writing-rules.md](references/writing-rules.md) — The 53 writing rules with explanations and examples. Read it when you apply the rules to your text.
- [references/word-selection.md](references/word-selection.md) — The procedure to select words correctly. Read it before you select or replace words.
- [references/dictionary-approved.md](references/dictionary-approved.md) — The 875 approved words with their approved meanings. Find a word here to make sure that it is approved.
- [references/dictionary-unapproved.md](references/dictionary-unapproved.md) — The 1274 words that are not approved. Find a word here to see its approved alternatives.

Scope: this skill does not cover GR-7 (inclusive language) because the user excluded it.
