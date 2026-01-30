# Questionnaire Error Handling Fix

## Problem
API error when saving questionnaire answers:
```
SQLSTATE[23503]: Foreign key violation: 7 ERROR: insert or update on table "jawaban_kuesioner_tamus" violates foreign key constraint "jawaban_kuesioner_tamus_pertanyaan_tamu_id_foreign"
DETAIL: Key (pertanyaan_tamu_id)=(1) is not present in table "pertanyaan_tamus".
```

## Root Cause
- No questionnaires (`KuesionerTamu`) existed in the database
- No questions (`PertanyaanTamu`) existed in the database
- Frontend was trying to save answers with `pertanyaan_tamu_id=1` which didn't exist
- Foreign key constraint prevented the insert

## Solution

### 1. Enhanced Error Messages
Updated both API and main controllers to provide clear error messages:

**Before:**
```json
{
    "success": false,
    "message": "Kuesioner tidak ditemukan untuk project dan area ini"
}
```

**After:**
```json
{
    "success": false,
    "message": "Project ini belum memiliki kuesioner. Harap segera membuat kuesioner untuk area ini.",
    "error_type": "no_questionnaire"
}
```

Or:
```json
{
    "success": false,
    "message": "Kuesioner ditemukan tetapi belum memiliki pertanyaan. Harap tambahkan pertanyaan ke kuesioner.",
    "error_type": "no_questions"
}
```

### 2. Question ID Validation
Added validation before saving answers to prevent foreign key violations:

```php
// Validate that all question IDs exist
$questionIds = array_keys($answers);
$existingQuestionIds = \App\Models\PertanyaanTamu::whereIn('id', $questionIds)->pluck('id')->toArray();
$invalidQuestionIds = array_diff($questionIds, $existingQuestionIds);

if (!empty($invalidQuestionIds)) {
    return response()->json([
        'success' => false,
        'message' => 'Pertanyaan dengan ID ' . implode(', ', $invalidQuestionIds) . ' tidak ditemukan. Harap pastikan kuesioner sudah dibuat dengan benar.',
        'error_type' => 'invalid_question_ids',
        'invalid_ids' => $invalidQuestionIds
    ], 400);
}
```

### 3. Enhanced Logging
Added detailed logging for debugging:

```php
\Log::info('API Questionnaire search', [
    'project_id' => $projectId,
    'area_id' => $areaId,
    'found' => $kuesioner ? true : false,
    'kuesioner_id' => $kuesioner ? $kuesioner->id : null,
    'questions_count' => $kuesioner && $kuesioner->pertanyaans ? $kuesioner->pertanyaans->count() : 0
]);
```

### 4. Test Data Creation
Created sample questionnaire and questions for testing:

- **Questionnaire**: "Kuesioner Kepuasan Tamu" for Project PHR Central (ID: 7), Area Kasikan (ID: 6)
- **Questions**: 3 sample questions with IDs 1, 2, 3

## Files Modified

### API Controller
- `app/Http/Controllers/Api/BukuTamuController.php`
  - Enhanced `getKuesionerByArea()` method
  - Enhanced `saveGuestQuestionnaire()` method
  - Added question ID validation
  - Added detailed logging

### Main Controller
- `app/Http/Controllers/Perusahaan/BukuTamuController.php`
  - Enhanced `getKuesionerByArea()` method
  - Enhanced `saveGuestQuestionnaire()` method
  - Added question ID validation

## Error Types

The API now returns specific error types for better frontend handling:

1. **`no_questionnaire`**: No questionnaire exists for the project/area
2. **`no_questions`**: Questionnaire exists but has no questions
3. **`invalid_question_ids`**: Trying to save answers for non-existent questions

## Testing

### User Context
- **Email**: 456deny@gmail.com
- **Password**: nicepatrol
- **Company**: ID 1
- **Available Projects**: 11 projects with various areas

### Test Questionnaire
- **Project**: PHR Central (ID: 7)
- **Area**: Kasikan (ID: 6)
- **Questions**: 3 questions (IDs: 1, 2, 3)

### API Endpoints
- `GET /api/v1/buku-tamu-kuesioner-by-area?project_id=7&area_id=6`
- `POST /api/v1/buku-tamu/{id}/questionnaire`

## Benefits

1. ✅ **Clear Error Messages**: Users know exactly what's missing
2. ✅ **Prevents Database Errors**: Validation prevents foreign key violations
3. ✅ **Better Debugging**: Detailed logging for troubleshooting
4. ✅ **User-Friendly**: Actionable error messages
5. ✅ **Robust**: Handles edge cases gracefully

## Next Steps

1. **Admin Interface**: Create UI for managing questionnaires
2. **Bulk Creation**: Tool to create questionnaires for all projects/areas
3. **Templates**: Predefined questionnaire templates
4. **Validation Rules**: More sophisticated question validation