# Word selection in STE

**Source:** ASD-STE100 Issue 9 (2025-01-15), Part 2 (Dictionary) Introduction, pages 2-0-3 to 2-0-19. The word-selection flowchart is the graphic on page 2-0-16.

General facts (page 2-0-3):

- The dictionary gives **875 approved words** — the most frequent words used in technical writing — with examples that show how to use each one correctly.
- It also includes a selection of **1274 words that are not approved**.
- For each non-approved word there are **one or more approved alternatives**; for each alternative there is an example that communicates the same information correctly — either a **word-for-word replacement** or a **different sentence construction**.

Other general facts about the dictionary:

- It does **not** include technical nouns or technical verbs as headwords, but some appear as alternatives, identified by **(TN)** (technical noun) or **(TV)** (technical verb).
- Entries do **not** include antonyms (opposite meanings) of the listed word.
- Spelling is **American English** as defined in **Merriam-Webster's dictionary**; punctuation in the examples obeys American English rules.

Illustration entries from the introduction:

- `AID (n)` — approved; meaning "Help that is given".
- `accuracy (n)` — not approved → **PRECISION (n)**. STE: "THE PRECISION OF THE ADJUSTMENT CAN CHANGE." / Non-STE: "The accuracy of the adjustment can vary."

Related material stated elsewhere in the specification: **Rule 1.5** (conditions to use technical nouns), **Rule 1.12** (conditions to use technical verbs), **Part 1, Section 1** (parts of speech), **Part 1, Section 3** (verbs).

---

## How to select words

From page 2-0-16: "STE is a controlled natural language with a restricted dictionary. As a result, it is not possible to use all the words that you want. If you are not sure about a word that you want to use, refer to this flowchart."

The complete decision flow:

1. **Is the word in the dictionary?**
   - Yes → go to step 2.
   - No → go to step 6.
2. **Is the word approved?** (an UPPERCASE headword)
   - Yes → go to step 3.
   - No → go to step 5.
3. **Does the word have the same part of speech** (as your intended use)?
   - Yes → read the meaning and the related examples, then go to step 4.
   - No → select the correct alternative, then go to step 5a.
4. **Is the meaning correct** (the approved meaning says what you want to say)?
   - Yes → **USE THE WORD.** (end)
   - No → do not use the word with this meaning; go to step 6.
5. **Read the alternative, its meaning, and the related examples.** Select the correct alternative, then go to step 5a.
   - 5a. **Does the alternative have the same part of speech?**
     - Yes → **do a word-for-word replacement.** (end)
     - No → **use a different sentence construction.** (end)
6. **Is the word a technical noun or a technical verb?** (Refer to the technical noun and technical verb categories — Rules 1.5 and 1.12.)
   - Yes → add the word to the **project glossary** with the applicable technical noun or technical verb category, then **USE THE WORD.** (end)
   - No → **DO NOT USE THE WORD.** (end)

Terminal states of the flowchart:

- **"Use the word."** (green) — reached from step 4 (approved word, correct part of speech, correct meaning) or from step 6 (technical noun/verb added to the project glossary).
- **"Do not use the word."** (red) — reached from step 6 when the word is not in the dictionary (or its meaning is not approved) and it is not a technical noun or technical verb.
- **"Do a word-for-word replacement."** (orange) — reached from step 5a when the selected alternative has the same part of speech.
- **"Use a different sentence construction."** (orange) — reached from step 5a when the selected alternative has a different part of speech.

---

## The dictionary format

The dictionary has **four columns** (page 2-0-3):

| Column | Content |
| --- | --- |
| 1 — Word (part of speech) | The word and its part of speech. |
| 2 — Approved meaning/ALTERNATIVES | The approved meaning of an approved word, OR the approved alternatives for a word that is not approved. |
| 3 — STE EXAMPLE | Examples that contain correct words and constructions in STE. |
| 4 — Non-STE example | Examples that contain words that are not approved and constructions that are not permitted in STE. |

### Column 1: Word (part of speech) (pages 2-0-4 to 2-0-8)

- All words are in **bold** typeface.
- A word in **UPPERCASE** letters = **approved**; you **can** use it. Examples: `ABRASIVE (adj)` — "That can remove material by friction"; `AID (n)` — "Help that is given".
- A word in **lowercase** letters = **not approved**; you **cannot** use it. Replace it with a different word or a different sentence construction. Examples: `main (adj)` → **PRIMARY (adj)**; `build (v)` → **ASSEMBLE (v)** ("ASSEMBLE THE UNIT.", not "Build the unit.").
- The part of speech is in parentheses with its recognized abbreviation. **Use an approved word only as the specified part of speech** (refer to Part 1, Section 1).

### The eight parts of speech

1. **noun (n)** — the name of a person, place, object, idea, quality, or activity.
2. **verb (v)** — shows a state of being or an action; its tense (present, past, future) tells you when the action occurs.
3. **adjective (adj)** — gives details about a noun, noun phrase, or multi-word noun (type, size, color, number). Can be qualitative or quantitative, can have comparative and superlative forms, and can come before nouns or after verbs.
4. **adverb (adv)** — modifies a verb, an adjective, or a different adverb; answers "how", "where", "when", "how often", and "how much".
5. **pronoun (pron)** — replaces a noun, noun phrase, or multi-word noun.
6. **article (art)** — tells you if a noun phrase is new (indefinite = *a, an*) or one you already know (definite = *the*).
7. **preposition (prep)** — shows how a noun, noun phrase, multi-word noun, or pronoun is related to other parts of the sentence.
8. **conjunction (conj)** — a word or phrase that connects words, phrases, and clauses.

### Forms of approved words

- **Nouns:** given only in the **singular** form; the **plural of countable nouns is permitted** unless the help says differently. Some nouns that are not approved can still be **technical nouns** in some contexts — Rule 1.5 gives the conditions. Example: `AGENT (n)` — "One of a group of materials made to do a specified task".
- **Verbs:** given only in the **permitted verb forms** — see "Verbs in STE" below.
- **Adjectives:** given in the **base form** with the **comparative and superlative** forms in parentheses, e.g. `SLOW (adj) (SLOWER, SLOWEST)` — "MAKE SURE THAT THE MOVEMENT OF THE ELEVATORS IS SLOW." Adjectives that form their comparative/superlative with "more" and "most" do not have those forms listed (MORE and MOST are approved words).
- **Adverbs:** frequently (but not always) made from an adjective plus "-ly" (e.g. `SLOWLY (adv)` — "In a slow manner"; `briskly (adv)` → **QUICKLY (adv)**). You make their comparative/superlative with "more" and "most"; those forms are not listed.

### Column 2: Approved meaning/ALTERNATIVES (pages 2-0-9 to 2-0-10)

- For an **approved word**, this column gives the approved meaning (definition). **The definition text is not itself STE.** If a meaning is not given in the dictionary, you cannot use the word with that meaning — use a different word that has the meaning you want. Example: `BEHIND (prep)` — "In a position at the rear of".
- For a **word that is not approved**, this column gives the approved alternatives, in UPPERCASE. The alternatives are **only suggestions** to help you. Example: `addition (n)` → **ADD (v)** — STE: "TO GET THE CORRECT CLEARANCE, ADD SPECIAL SHIMS, AS NECESSARY." / Non-STE: "Adjust the clearance by the addition of special shims, as necessary."
- An approved alternative can have a **different part of speech** from the non-approved word. Usually the **first** alternative has the **same** part of speech. Example: `maintain (v)` → **KEEP (v)**, **HOLD (v)**, **MAINTENANCE (n)**.
- Column 2 can include **technical nouns/verbs as alternatives**, marked **(TN)** or **(TV)**. Example: `uncovered (v)` → **COVER (TN)** ("DO NOT PUT A COVER ON THE CONTAINER.", not "Leave the container uncovered.").
- An alternative can be a **phrase**; phrase alternatives have **no part-of-speech marker**. Example: `simultaneously (adv)` → **AT THE SAME TIME** ("DO THESE TWO STEPS AT THE SAME TIME.").

### Column 3: STE EXAMPLE (page 2-0-14)

- Shows how to use the approved word, how to use the approved alternative (usually a word-for-word replacement), and how to keep the same meaning with a different sentence construction.
- The examples are **recommendations** — each shows one method; different constructions with other approved words can keep the same meaning.
- Many examples come from **aircraft maintenance**; adapt them to other subject fields/domains by replacing the terms. You can send a change form with your own examples for possible inclusion in the dictionary.
- Examples: `A (art)` — "Function word: indefinite article"; `manufacture (v)` → **MAKE (v)** — STE: "YOU CAN MAKE THE CLEARING TOOL LOCALLY." / Non-STE: "The clearing tool can be manufactured locally."

### Column 4: Non-STE example (page 2-0-15)

- Shows how a non-approved word is used in standard technical English, to help you apply the approved alternatives or different constructions.
- One non-STE example can contain **more than one** non-approved word or non-permitted construction; the STE example replaces them all.
- For **approved words, Column 4 is empty** — unless there is help about other/restricted meanings. Example: `ABOVE (prep)` = "In (or to) a position farther up than something"; for other meanings use **MORE THAN** ("THE PRESSURE VALUE MUST BE MORE THAN 800 kPa.", not "The pressure value must be above 800 kPa.").

---

## Verbs in STE

(Pages 2-0-5 to 2-0-7.) Approved verb forms:

- Verbs are given in the dictionary only in the verb forms that are **permitted** (refer to Part 1, Section 3).
- **Do not use verb forms that are not in the dictionary.**
- The meaning of a verb can differ if it has an object (**transitive**) or no object (**intransitive**).
- A verb can have more than one approved meaning and more than one STE example.
- Some verbs that are not approved can be **technical verbs** in some contexts — Rule 1.12 gives all necessary information and conditions.

Verbs in the dictionary can be one of **four types**:

1. **Regular verbs** — obey a constant pattern for the simple past tense and the past participle, usually adding **"-ed"** to the base form for both. The dictionary shows four forms (base, -s, past, past participle). Do not use the past participle form if it is not in the dictionary.
   - Example: `ADAPT (v), ADAPTS, ADAPTED, ADAPTED` — "To change or adjust to that which is necessary." STE: "ADAPT THE PRESSURE CONNECTION TO THE PITOT HEAD." / "THE SYSTEM INTERFACE CIRCUITS ADAPT TO THE PHYSICAL PROPERTIES OF THE CONNECTED SYSTEMS."
2. **Irregular verbs** — do NOT obey the standard rules for the simple past tense and the past participle.
   - Example: `GIVE (v), GIVES, GAVE, GIVEN` — "To provide." STE: "THIS SECTION GIVES THE CLEANING PROCEDURES FOR THE DISASSEMBLED PARTS."
3. **Irregular auxiliary verbs** — auxiliary verbs with unusual forms for tenses.
   - Example: `BE (v), IS, WAS (also ARE, WERE)` — **no other verb forms**. Meanings: (1) "To occur, exist" — STE: "IF THERE IS CORROSION ON THE PUMP VANES, REPLACE THE PUMP."; (2) "To have a property to be equal to" — STE: "ACID SOLUTIONS ARE DANGEROUS."
4. **Defective modal verbs** — modal verbs in which some verb forms are missing.
   - Example: `CAN (v), CAN, COULD` — **no other verb forms permitted**. "Auxiliary modal verb that means to be possible, to be able to, or to be permitted to." STE: "A MIXTURE OF FUEL AND OXYGEN CAN CAUSE AN EXPLOSION." / "YOU CAN CLEAN THE DRAIN HOLES WITH THE CLEANING TOOL." / "YOU CAN OPERATE THE VEHICLE AFTER THE INSPECTION IS COMPLETED."
   - Help for CAN: **do not use COULD (v) to show possibility** — STE: "IF YOU DO NOT OBEY THIS WARNING, AN EXPLOSION CAN OCCUR." / Non-STE: "...an explosion could occur."
   - Example: `WILL (v)` — **no other verb forms**. "Auxiliary modal verb that shows simple future tense." STE: "WARNINGS AND CAUTIONS IN THIS MANUAL WILL HELP YOU TO DO THE WORK SAFELY AND CORRECTLY."

---

## Help categories

Some entries have a graphic symbol (light-bulb icon) plus text that tells you to use a different approved alternative or a different sentence construction. This is called **"help"**. There are four categories (pages 2-0-11 to 2-0-13):

1. **Recommendations** — gives more information or instructions about how to use the approved word correctly.
   - Example: `PUSH (v), PUSHES, PUSHED, PUSHED` — meanings (1) "To apply a force to something to move it away from the source of the force"; (2) "To move with a force against something." Help: *Use this word together with a preposition or an adverb to show direction.*
2. **Restricted meaning** — tells you that some approved words have a **restricted** meaning, so you must use approved alternatives for the other meanings. When help refers to restricted meanings, the dictionary gives STE and non-STE examples in columns 3 and 4.
   - Example: `ABOUT (prep)` = "Concerned with" — STE: "FOR DATA ABOUT THE LOCATION OF CIRCUIT BREAKERS, REFER TO THE WIRING LIST." For other meanings use **APPROXIMATELY (adv)** ("DRAIN APPROXIMATELY 2 LITERS OF FUEL FROM THE TANK.", not "Drain about 2 liters...") or **AROUND (prep)** ("TURN THE SHAFT AROUND ITS AXIS.", not "Rotate the shaft about its axis.").
3. **Single-context words** — be careful with words that are approved for only **one context**; you must not use them in other contexts.
   - Example: `SWALLOW (v), SWALLOWS, SWALLOWED, SWALLOWED` = "To take through the mouth and esophagus into the stomach." Help: *Use this word for safety instructions only.* STE: "IF YOU SWALLOW NITRIC ACID, DO NOT CAUSE VOMITING. GET MEDICAL AID IMMEDIATELY."
4. **Important information** — gives important information about the listed words, including non-approved ones.
   - Examples: `BE (v)` (see "Verbs in STE"); `already (adv)` → **IN PROGRESS (adv)**, **NO OTHER** — Help: *Frequently, an alternative for this word is not necessary.* STE: "THE DATABASE SYNCHRONIZATION IS IN PROGRESS." / "YOU CAN DO THIS REPAIR PROCEDURE ONLY IF THERE IS NO OTHER DAMAGE." / "MAKE SURE THAT THE SOFTWARE IS INSTALLED IN THE REPOSITORY."

---

## Recurring errors

The most frequently recurring errors that writers make in STE (pages 2-0-17 to 2-0-18). **If a word is not approved in the dictionary, do not use it.** Complete list — 39 mappings (Non-STE → STE):

| # | Non-STE | STE |
| --- | --- | --- |
| 1 | acceptable (adj) | PERMITTED (adj) |
| 2 | alternate (adj) | ALTERNATIVE (adj) |
| 3 | any (adj) | None or a different sentence construction |
| 4 | avoid (v) | PREVENT (v) |
| 5 | both (adj) | THE TWO (TN) |
| 6 | check (v) | CHECK (n) |
| 7 | cover (v) | COVER (TN) |
| 8 | complete (adj) | COMPLETED (adj) |
| 9 | damage (v) | DAMAGE (n) |
| 10 | ensure (v) | MAKE SURE (v) |
| 11 | fit (v) | INSTALL (v) |
| 12 | follow (v) | OBEY (v) |
| 13 | further (adj) | MORE (adj) |
| 14 | further (adv) | MORE (adv) |
| 15 | have to (v) | Use an action verb in the imperative form |
| 16 | however (adv) | BUT (conj) |
| 17 | insert (v) | PUT (v) |
| 18 | main (adj) | PRIMARY (adj) |
| 19 | may (v) | CAN (v) |
| 20 | need (v) | NECESSARY (adj) |
| 21 | now (adv) | AT THIS TIME |
| 22 | old (adj) | REMAINING (adj), USED (adj), EXPIRED (adj) |
| 23 | over (prep) | ABOVE (prep), ON (prep), ALONG (prep) |
| 24 | people (n) | PERSON (n), PERSONNEL (n) |
| 25 | perform (v) | DO (v) |
| 26 | portion (n) | PART (n) |
| 27 | press (v) | PUSH (v) |
| 28 | reach (v) | GET (v) |
| 29 | repeat (v) | DO (v) … AGAIN |
| 30 | required (v) | NECESSARY (adj) |
| 31 | rotate (v) | TURN (v) |
| 32 | secure (v) | ATTACH (v), SAFETY (v) |
| 33 | shall (v) | MUST (v) |
| 34 | should (v) | MUST (v) |
| 35 | since (conj) | BECAUSE (conj) |
| 36 | test (v) | TEST (n) |
| 37 | therefore (adv) | THUS (adv), AS A RESULT |
| 38 | under (prep) | BELOW (prep), IN (prep), LESS THAN |
| 39 | using (v) | USE (v), WITH (prep) |

Notes on the list:

- `further (adj)` and `further (adv)` are **two separate entries** (different parts of speech, both mapping to MORE).
- In the printed specification the list runs 31 entries on page 2-0-17 and 8 entries on the continued page 2-0-18 (31 + 8 = 39).
- Some replacements change the part of speech (e.g. `check (v)` → CHECK (n), `need (v)` → NECESSARY (adj)) — these need a different sentence construction, not a word-for-word swap.
- Two replacements are technical nouns: THE TWO (TN) and COVER (TN).

---

## Approved verbs

The dictionary's quick-reference list of approved verbs (page 2-0-19). Every entry is an approved verb `(v)`. **Total = 208 verbs.** Letters **J, N, Q, V, X, Y, Z have no approved verbs.**

- **A (12):** ABSORB, ACCEPT, ACTIVATE, ADAPT, ADD, ADJUST, AGREE, ALIGN, APPLY, ARM, ASSEMBLE, ATTACH
- **B (11):** BALANCE, BE, BECOME, BEND, BLEED, BLOW, BOND, BREAK, BREATHE, BURN, BYPASS
- **C (25):** CALCULATE, CALIBRATE, CAN, CANCEL, CANNOT, CATCH, CAUSE, CHANGE, CHARGE, CLEAN, CLOSE, COLLECT, COME, COME ON, COMPARE, COMPLETE, COMPRESS, CONNECT, CONTACT, CONTAIN, CONTINUE, CONTROL, CORRECT, COUNT, CUT
- **D (16):** DEACTIVATE, DECREASE, DE-ENERGIZE, DEFLATE, DEFUEL, DEPLOY, DISARM, DISASSEMBLE, DISCARD, DISCONNECT, DISENGAGE, DIVIDE, DO, DRAIN, DRINK, DRY
- **E (9):** EAT, EJECT, ENERGIZE, ENGAGE, ERASE, EXAMINE, EXPAND, EXTEND, EXTINGUISH
- **F (12):** FALL, FEATHER, FEEL, FILL, FIND, FIRE, FLASH, FLOW, FLUSH, FOLD, FOLLOW, FREEZE
- **G (5):** GET, GIVE, GO, GO OFF, GROUND
- **H (6):** HANG, HAVE, HEAR, HELP, HIT, HOLD
- **I (9):** IDENTIFY, IGNORE, ILLUMINATE, INCLUDE, INCREASE, INFLATE, INSTALL, INTERCHANGE, ISOLATE
- **J (0):** none
- **K (3):** KEEP, KILL, KNOW
- **L (9):** LATCH, LET, LIFT, LISTEN, LOCK, LOOK, LOOSEN, LOWER, LUBRICATE
- **M (10):** MAKE, MAKE SURE, MEASURE, MELT, MIX, MONITOR, MOOR, MOVE, MULTIPLY, MUST
- **N (0):** none
- **O (5):** OBEY, OCCUR, OPEN, OPERATE, OVERRIDE
- **P (12):** PAINT, PARK, POINT, POLISH, PREPARE, PRESSURIZE, PREVENT, PROTRUDE, PULL, PUSH, PUT, PUT ON
- **Q (0):** none
- **R (14):** READ, RECEIVE, RECOMMEND, RECORD, RECYCLE, REFER, REFUEL, REJECT, RELEASE, REMOVE, REPAIR, REPLACE, RETRACT, RUB
- **S (24):** SAFETY, SCHEDULE, SEAL, SEE, SELECT, SEND, SENSE, SET, SHAKE, SHOW, SIMULATE, SMELL, SMOKE, SOAK, SPEAK, SPILL, SPRAY, START, STAY, STOP, STOW, SUBTRACT, SUPPLY, SWALLOW
- **T (14):** TAG, TAP, TELL, THINK, TIGHTEN, TILT, TORQUE, TOUCH, TOW, TRANSMIT, TRY, TUNE, TURN, TWIST
- **U (4):** UNFOLD, UNLOCK, UNWIND, USE
- **V (0):** none
- **W (8):** WAIT, WALK, WANT, WEAR, WEIGH, WILL, WIND, WRITE
- **X (0):** none
- **Y (0):** none
- **Z (0):** none

Letter-count check: A 12 + B 11 + C 25 + D 16 + E 9 + F 12 + G 5 + H 6 + I 9 + K 3 + L 9 + M 10 + O 5 + P 12 + R 14 + S 24 + T 14 + U 4 + W 8 = **208**.

Notes on the list:

- Multi-word verbs (COME ON, GO OFF, MAKE SURE, PUT ON) and the hyphenated DE-ENERGIZE each count as **one** entry; CAN and CANNOT are separate entries.
- The auxiliary and modal verbs (BE, CAN, CANNOT, HAVE, MUST, WILL) appear in this list like any other approved verb — but only in their permitted forms (see "Verbs in STE").
- In the printed specification this table is set in five interleaved columns; it is de-interleaved into a single alphabetical sequence here.
