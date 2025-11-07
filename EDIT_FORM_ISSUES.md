# EDIT FORM CRITICAL ISSUES FOUND!

## Summary: SAME 19 FIELD MISMATCHES AS CREATE FORM!

The edit form (edit_record.php) and update file (update_record.php) have **THE SAME FIELD NAME MISMATCHES** as the create form!

---

## Section 1: Display vs Update Mismatches

### Issue #1: FIELD NAMES in edit_record.php are WRONG!

The edit form shows data correctly but sends the **WRONG field names** during update!

| Line | Field Display | Form Field Name | Update Expects | Status |
|------|---------------|----------------|----------------|--------|
| 579 | initial_resp | `name="initial_resp"` | `initial_resp_rate` | ❌ MISMATCH |
| 682 | followup_resp | `name="followup_resp"` | `followup_resp_rate` | ❌ MISMATCH |
| 455 | medical_specify | `name="medical_specify"` | Checks for `medical_specify` | ✅ OK |
| 464 | trauma_specify | `name="trauma_specify"` | Checks for `trauma_specify` | ✅ OK |
| 473 | ob_specify | `name="ob_specify"` | Checks for `ob_specify` | ✅ OK |
| 482 | general_specify | `name="general_specify"` | Checks for `general_specify` | ✅ OK |
| 864 | face_drooping | `name="face_drooping"` | Checks for `face_drooping` | ❌ MISMATCH |
| 879 | arm_weakness | `name="arm_weakness"` | Checks for `arm_weakness` | ❌ MISMATCH |
| 894 | speech_difficulty | `name="speech_difficulty"` | Checks for `speech_difficulty` | ❌ MISMATCH |
| 909 | time_to_call | `name="time_to_call"` | Checks for `time_to_call` | ❌ MISMATCH |
| 923 | sample_details | `name="sample_details"` | Checks for `sample_details` | ❌ MISMATCH |
| 933 | baby_status | `name="baby_status"` | Checks for `baby_status` | ❌ MISMATCH |
| 938 | delivery_time | `name="delivery_time"` | Checks for `delivery_time` | ❌ MISMATCH |
| 945 | placenta | `name="placenta"` | Checks for `placenta` | ❌ MISMATCH |
| 958 | lmp | `name="lmp"` | Saves as `ob_lmp` | ❌ MISMATCH |
| 963 | aog | `name="aog"` | Saves as `ob_aog` | ❌ MISMATCH |
| 968 | edc | `name="edc"` | Saves as `ob_edc` | ❌ MISMATCH |
| 1005 | aider1 | `name="aider1"` | Checks for `aider1` | ❌ MISMATCH |
| 1010 | aider2 | `name="aider2"` | Checks for `aider2` | ❌ MISMATCH |

---

## Issue #2: DATABASE DISPLAY vs FORM NAMES

The edit form **displays** data from database correctly:
- Shows `$record['initial_resp_rate']` ✅ (line 580)
- Shows `$record['followup_resp_rate']` ✅ (line 683)
- Shows `$record['fast_face_drooping']` ✅ (line 865)
- Shows `$record['fast_arm_weakness']` ✅ (line 880)
- Shows `$record['fast_speech_difficulty']` ✅ (line 895)
- Shows `$record['fast_time_to_call']` ✅ (line 910)
- Shows `$record['fast_sample_details']` ✅ (line 923)
- Shows `$record['ob_baby_status']` ✅ (line 934)
- Shows `$record['ob_delivery_time']` ✅ (line 939)
- Shows `$record['ob_placenta']` ✅ (line 946/951)
- Shows `$record['ob_lmp']` ✅ (line 959)
- Shows `$record['ob_aog']` ✅ (line 964)
- Shows `$record['ob_edc']` ✅ (line 969)
- Shows `$record['first_aider']` ✅ (line 1006)
- Shows `$record['second_aider']` ✅ (line 1011)

BUT sends data with **WRONG field names** that update_record.php ALSO expects incorrectly!

---

## Issue #3: update_record.php Expects WRONG Names Too!

update_record.php file expects:
- `initial_resp` (line 151) - Should be `initial_resp_rate`
- `followup_resp` (line 163) - Should be `followup_resp_rate`
- `face_drooping` (line 179) - Should be `fast_face_drooping`
- `arm_weakness` (line 180) - Should be `fast_arm_weakness`
- `speech_difficulty` (line 181) - Should be `fast_speech_difficulty`
- `time_to_call` (line 182) - Should be `fast_time_to_call`
- `sample_details` (line 183) - Should be `fast_sample_details`
- `baby_status` (line 186) - Should be `ob_baby_status`
- `delivery_time` (line 187) - Should be `ob_delivery_time`
- `placenta` (line 188) - Should be `ob_placenta`
- `lmp` (line 189) - Should be `ob_lmp`
- `aog` (line 190) - Should be `ob_aog`
- `edc` (line 191) - Should be `ob_edc`
- `aider1` (line 198) - Should be `first_aider`
- `aider2` (line 199) - Should be `second_aider`

---

## CRITICAL PROBLEM:

**THE EDIT FORM WILL NOT SAVE UPDATES CORRECTLY!**

When users edit a record:
1. ✅ The form **DISPLAYS** the data correctly from database
2. ❌ BUT when they submit, field names are **WRONG**
3. ❌ update_record.php expects **WRONG** field names
4. ❌ So updates will **FAIL** or save to **WRONG** fields!

---

## Solution Required:

### Option 1: Match Everything to CREATE Form (RECOMMENDED)
Update both edit_record.php and update_record.php to use the **SAME corrected field names** as TONYANG.php and TONYANG_save.php

### Option 2: Keep Current Names
Would require reverting all changes to create form (NOT recommended)

---

## Files That Need Fixing:

1. **edit_record.php** - Update 15+ field names
2. **update_record.php** - Update 15+ field name checks

---

**RECOMMENDATION:** Fix edit_record.php and update_record.php to match the corrections already made to TONYANG.php and TONYANG_save.php!
