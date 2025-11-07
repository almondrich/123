# TONYANG Form Field Analysis Report

## CRITICAL ISSUES FOUND

### Section 1: Basic Information
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| form_date | form_date | $form_date | ✅ OK | 111 | 111 |
| departure_time | departure_time | $departure_time | ✅ OK | 115 | 120 |
| arrival_time | arrival_time | $arrival_time | ✅ OK | 119 | 121 |
| vehicle_used | vehicle_used | $vehicle_used | ✅ OK | 127,131,135 | 122 |
| vehicle_details | vehicle_details | $vehicle_details | ✅ OK | 139 | 123 |
| **driver** | **driver** | **$driver_name** | ❌ **MISMATCH** | 193 | 124 |
| arrival_scene_location | arrival_scene_location | $arrival_scene_location | ✅ OK | 145 | 135 |
| arrival_scene_time | arrival_scene_time | $arrival_scene_time | ✅ OK | 149 | 136 |
| departure_scene_location | departure_scene_location | $departure_scene_location | ✅ OK | 156 | 137 |
| departure_scene_time | departure_scene_time | $departure_scene_time | ✅ OK | 160 | 138 |
| arrival_hospital_name | arrival_hospital_name | $arrival_hospital_name | ✅ OK | 167 | 141 |
| arrival_hospital_time | arrival_hospital_time | $arrival_hospital_time | ✅ OK | 171 | 142 |
| departure_hospital_location | departure_hospital_location | $departure_hospital_location | ✅ OK | 178 | 143 |
| departure_hospital_time | departure_hospital_time | $departure_hospital_time | ✅ OK | 182 | 144 |
| **arrival_station** | **arrival_station** | **$arrival_station_time** | ❌ **MISMATCH** | 189 | 145 |
| **persons_present[]** | **persons_present[]** | **Individual checkboxes** | ❌ **WRONG LOGIC** | 201-218 | 149-155 |

### Section 2: Patient Information
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| patient_name | patient_name | $patient_name | ✅ OK | 235 | 158 |
| date_of_birth | date_of_birth | $date_of_birth | ✅ OK | 239 | 159 |
| age | age | $age | ✅ OK | 243 | 160 |
| gender | gender | $gender | ✅ OK | 252,256 | 161 |
| civil_status | civil_status | $civil_status | ✅ OK | 265,269 | 175 |
| address | address | $address | ✅ OK | 279 | 176 |
| zone | zone | $zone | ✅ OK | 283 | 177 |
| occupation | occupation | $occupation | ✅ OK | 290 | 178 |
| place_of_incident | place_of_incident | $place_of_incident | ✅ OK | 294 | 179 |
| zone_landmark | zone_landmark | $zone_landmark | ✅ OK | 301 | 180 |
| incident_time | incident_time | $incident_time | ✅ OK | 305 | 181 |
| informant_name | informant_name | $informant_name | ✅ OK | 316 | 184 |
| informant_address | informant_address | $informant_address | ✅ OK | 320 | 185 |
| arrival_type | arrival_type | $arrival_type | ✅ OK | 329,333 | 186 |
| call_arrival_time | call_arrival_time | $call_arrival_time | ✅ OK | 340 | 187 |
| contact_number | contact_number | $contact_number | ✅ OK | 344 | 188 |
| relationship_victim | relationship_victim | $relationship_victim | ✅ OK | 351 | 189 |
| personal_belongings[] | personal_belongings[] | $personal_belongings | ✅ OK | 355 | 192-197 |
| other_belongings | other_belongings | $other_belongings | ✅ OK | 372 | 198 |

### Section 3: Emergency & Care
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| **emergency_type[]** | **emergency_type[]** | **Individual checkboxes** | ❌ **WRONG LOGIC** | 387,394,401,408 | 201-208 |
| **medical_specify** | **medical_specify** | **$emergency_medical_details** | ❌ **MISMATCH** | 390 | 202 |
| **trauma_specify** | **trauma_specify** | **$emergency_trauma_details** | ❌ **MISMATCH** | 397 | 204 |
| **ob_specify** | **ob_specify** | **$emergency_ob_details** | ❌ **MISMATCH** | 404 | 206 |
| **general_specify** | **general_specify** | **$emergency_general_details** | ❌ **MISMATCH** | 411 | 208 |
| **care_management[]** | **care_management[]** | **Individual checkboxes** | ❌ **WRONG LOGIC** | 422-447 | 212-218 |
| oxygen_lpm | oxygen_lpm | $oxygen_lpm | ✅ OK | 455 | 219 |
| other_care | other_care | $other_care | ✅ OK | 459 | 220 |

### Section 4: Vitals
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| initial_time | initial_time | $initial_time | ✅ OK | 475 | 223 |
| initial_bp | initial_bp | $initial_bp | ✅ OK | 479 | 224 |
| initial_temp | initial_temp | $initial_temp | ✅ OK | 483 | 225 |
| initial_pulse | initial_pulse | $initial_pulse | ✅ OK | 487 | 226 |
| **initial_resp** | **initial_resp** | **$initial_resp_rate** | ❌ **MISMATCH** | 494 | 227 |
| initial_pain_score | initial_pain_score | $initial_pain_score | ✅ OK | 498 | 228 |
| initial_spo2 | initial_spo2 | $initial_spo2 | ✅ OK | 502 | 229 |
| initial_spinal_injury | initial_spinal_injury | $initial_spinal_injury | ✅ OK | 508,512 | 230 |
| initial_consciousness | initial_consciousness | $initial_consciousness | ✅ OK | 524-537 | 231 |
| initial_helmet | initial_helmet | $initial_helmet | ✅ OK | 545,549 | 232 |
| followup_time | followup_time | $followup_time | ✅ OK | 563 | 235 |
| followup_bp | followup_bp | $followup_bp | ✅ OK | 567 | 236 |
| followup_temp | followup_temp | $followup_temp | ✅ OK | 571 | 237 |
| followup_pulse | followup_pulse | $followup_pulse | ✅ OK | 575 | 238 |
| **followup_resp** | **followup_resp** | **$followup_resp_rate** | ❌ **MISMATCH** | 582 | 239 |
| followup_pain_score | followup_pain_score | $followup_pain_score | ✅ OK | 586 | 240 |
| followup_spo2 | followup_spo2 | $followup_spo2 | ✅ OK | 590 | 241 |
| followup_spinal_injury | followup_spinal_injury | $followup_spinal_injury | ✅ OK | 596,600 | 242 |
| followup_consciousness | followup_consciousness | $followup_consciousness | ✅ OK | 611-624 | 243 |

### Section 5: Assessment
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| **chief_complaints[]** | **chief_complaints[]** | **Individual checkboxes** | ❌ **WRONG LOGIC** | 641-662 | 246-252 |
| other_complaints | other_complaints | $other_complaints | ✅ OK | 669 | 254 |
| **injuries_data** | **injuries_data** | **$injuries_data** | ❌ **MISMATCH** | 744 | 290 |
| **face_drooping** | **face_drooping** | **$fast_face_drooping** | ❌ **MISMATCH** | 754,758 | 257 |
| **arm_weakness** | **arm_weakness** | **$fast_arm_weakness** | ❌ **MISMATCH** | 767,771 | 258 |
| **speech_difficulty** | **speech_difficulty** | **$fast_speech_difficulty** | ❌ **MISMATCH** | 780,784 | 259 |
| **time_to_call** | **time_to_call** | **$fast_time_to_call** | ❌ **MISMATCH** | 793,797 | 260 |
| **sample_details** | **sample_details** | **$fast_sample_details** | ❌ **MISMATCH** | 805 | 261 |
| **baby_status** | **baby_status** | **$ob_baby_status** | ❌ **MISMATCH** | 815 | 264 |
| **delivery_time** | **delivery_time** | **$ob_delivery_time** | ❌ **MISMATCH** | 819 | 265 |
| **placenta** | **placenta** | **$ob_placenta** | ❌ **MISMATCH** | 825,829 | 266 |
| lmp | lmp | $ob_lmp | ✅ OK | 836 | 267 |
| aog | aog | $ob_aog | ✅ OK | 840 | 268 |
| edc | edc | $ob_edc | ✅ OK | 844 | 269 |

### Section 6: Team & Endorsement
| Field Name (HTML) | Expected POST Name | Save File Variable | Status | Line in Form | Line in Save |
|-------------------|-------------------|-------------------|--------|--------------|--------------|
| team_leader_notes | team_leader_notes | $team_leader_notes | ✅ OK | 859 | 272 |
| team_leader | team_leader | $team_leader | ✅ OK | 869 | 273 |
| data_recorder | data_recorder | $data_recorder | ✅ OK | 873 | 274 |
| logistic | logistic | $logistic | ✅ OK | 877 | 275 |
| **aider1** | **aider1** | **$first_aider** | ❌ **MISMATCH** | 884 | 276 |
| **aider2** | **aider2** | **$second_aider** | ❌ **MISMATCH** | 888 | 277 |
| endorsement | endorsement | $endorsement | ✅ OK | 899 | 280 |
| hospital_name | hospital_name | $hospital_name | ✅ OK | 903 | 281 |
| **[MISSING]** | **received_by** | **$received_by** | ❌ **MISSING IN FORM** | N/A | 282 |
| endorsement_datetime | endorsement_datetime | $endorsement_datetime | ✅ OK | 910 | 283 |
| endorsement_attachment | endorsement_attachment | $endorsement_attachment | ✅ OK | 919 | 284 |

## Summary of Issues

### Critical Mismatches (Field Names Don't Match):
1. **driver** → expecting **driver_name**
2. **arrival_station** → expecting **arrival_station_time**
3. **initial_resp** → expecting **initial_resp_rate**
4. **followup_resp** → expecting **followup_resp_rate**
5. **medical_specify** → expecting **emergency_medical_details**
6. **trauma_specify** → expecting **emergency_trauma_details**
7. **ob_specify** → expecting **emergency_ob_details**
8. **general_specify** → expecting **emergency_general_details**
9. **face_drooping** → expecting **fast_face_drooping**
10. **arm_weakness** → expecting **fast_arm_weakness**
11. **speech_difficulty** → expecting **fast_speech_difficulty**
12. **time_to_call** → expecting **fast_time_to_call**
13. **sample_details** → expecting **fast_sample_details**
14. **baby_status** → expecting **ob_baby_status**
15. **delivery_time** → expecting **ob_delivery_time**
16. **placenta** → expecting **ob_placenta**
17. **aider1** → expecting **first_aider**
18. **aider2** → expecting **second_aider**
19. **injuries_data** → expecting **injuries**

### Wrong Logic (Array vs Individual Checkbox):
1. **persons_present[]** - Form sends as array, but save file looks for individual checkbox names
2. **emergency_type[]** - Form sends as array, but save file looks for individual checkbox names
3. **care_management[]** - Form sends as array, but save file looks for individual checkbox names
4. **chief_complaints[]** - Form sends as array, but save file looks for individual checkbox names

### Missing Fields:
1. **received_by** - Save file expects it but form doesn't have it

## Recommendations

### Fix 1: Update Form Field Names (TONYANG.php)
Change the following name attributes:
- `name="driver"` → `name="driver_name"`
- `name="arrival_station"` → `name="arrival_station_time"`
- `name="initial_resp"` → `name="initial_resp_rate"`
- `name="followup_resp"` → `name="followup_resp_rate"`
- `name="medical_specify"` → `name="emergency_medical_details"`
- `name="trauma_specify"` → `name="emergency_trauma_details"`
- `name="ob_specify"` → `name="emergency_ob_details"`
- `name="general_specify"` → `name="emergency_general_details"`
- `name="face_drooping"` → `name="fast_face_drooping"`
- `name="arm_weakness"` → `name="fast_arm_weakness"`
- `name="speech_difficulty"` → `name="fast_speech_difficulty"`
- `name="time_to_call"` → `name="fast_time_to_call"`
- `name="sample_details"` → `name="fast_sample_details"`
- `name="baby_status"` → `name="ob_baby_status"`
- `name="delivery_time"` → `name="ob_delivery_time"`
- `name="placenta"` → `name="ob_placenta"`
- `name="aider1"` → `name="first_aider"`
- `name="aider2"` → `name="second_aider"`
- `name="injuries_data"` → `name="injuries"`

### Fix 2: Update Save Logic for Arrays (TONYANG_save.php)
Update the logic to properly handle array submissions for:
- persons_present[]
- emergency_type[]
- care_management[]
- chief_complaints[]

### Fix 3: Add Missing Field
Add a "received_by" field to the form or remove from save logic.
