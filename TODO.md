# TODO: Add Missing Fields to Edit Form

## Overview
The edit form (public/edit_record.php) is missing numerous fields compared to the main form (public/TONYANG.php). Need to add all missing fields following the exact pattern from the main form and update the API to handle them.

## Steps
1. **Update public/edit_record.php**
   - Add missing fields to Section 1: scene locations/times, hospital info, vehicle details
   - Add to Section 2: civil status, zone, zone/landmark, incident time, informant details, personal belongings
   - Add to Section 3: emergency call types, oxygen LPM, other care
   - Add to Section 4: initial time, resp rate, pain score, spinal injury, consciousness, helmet, all followup vitals
   - Add to Section 5: additional chief complaints, FAST assessment, OB section
   - Add to Section 6: first aider, second aider, hospital endorsement, waiver section

2. **Update api/update_record.php**
   - Add handling for all new fields in the database update query
   - Ensure proper sanitization and validation

3. **Test the form**
   - Verify all fields are populated with existing data
   - Test form submission and database update
