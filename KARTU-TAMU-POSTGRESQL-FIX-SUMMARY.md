# Kartu Tamu PostgreSQL Fix Summary

## Issue Fixed: PostgreSQL Query Syntax Error

### Problem
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "aktif" does not exist
```

**Root Cause**: PostgreSQL uses single quotes for string literals, not double quotes. The query was using double quotes around the string "aktif" which PostgreSQL interprets as a column identifier.

### Solution Applied

#### 1. Fixed Controller Query (KartuTamuController.php)

**Before** (Incorrect - Double Quotes):
```php
DB::raw('COUNT(CASE WHEN kartu_tamus.current_guest_id IS NULL AND kartu_tamus.status = "aktif" AND kartu_tamus.is_active = 1 THEN 1 END) as tersedia')
```

**After** (Correct - Single Quotes):
```php
DB::raw("COUNT(CASE WHEN kartu_tamus.current_guest_id IS NULL AND kartu_tamus.status = 'aktif' AND kartu_tamus.is_active = true THEN 1 END) as tersedia")
```

#### 2. PostgreSQL Compatibility Changes

**Boolean Values**:
- Changed `= 1` to `= true` for boolean fields
- PostgreSQL prefers explicit boolean values

**String Literals**:
- Changed `"aktif"` to `'aktif'` 
- PostgreSQL requires single quotes for string literals

#### 3. Model Scope Verification

**File**: `app/Models/KartuTamu.php`

Verified that the `scopeAvailable` method uses correct syntax:
```php
public function scopeAvailable($query)
{
    return $query->where('status', 'aktif')
                ->whereNull('current_guest_id')
                ->where('is_active', true);
}
```

### Files Modified

1. ✅ `app/Http/Controllers/Perusahaan/KartuTamuController.php`
   - Fixed `index()` method query
   - Updated PostgreSQL-compatible syntax

### Testing Data Added

**Created**: `database/seeders/KartuTamuSeeder.php`

**Sample Data**:
- 6 sample cards across 2 areas
- Different statuses: aktif, rusak, hilang
- NFC codes for testing
- Proper relationships with projects and areas

**Seeder Results**:
```
✅ KartuTamu seeder completed - created 6 cards
```

### PostgreSQL vs MySQL Differences

#### String Literals
- **MySQL**: Accepts both `"string"` and `'string'`
- **PostgreSQL**: Only accepts `'string'` for literals, `"string"` for identifiers

#### Boolean Values
- **MySQL**: Accepts `1/0` or `true/false`
- **PostgreSQL**: Prefers explicit `true/false`

#### Case Sensitivity
- **MySQL**: Case-insensitive by default
- **PostgreSQL**: Case-sensitive, uses `ILIKE` for case-insensitive search

### Verification Steps

1. ✅ **Migration ran successfully**
2. ✅ **Routes registered correctly**
3. ✅ **Sample data seeded**
4. ✅ **PostgreSQL syntax corrected**

### Next Steps

The Kartu Tamu module should now work correctly with PostgreSQL. The main functionality includes:

1. **Summary page** - Shows cards grouped by project/area
2. **Detail page** - Shows individual cards for specific area
3. **CRUD operations** - Create, read, update, delete cards
4. **Status management** - Track active/damaged/lost cards
5. **Statistics** - Real-time counts and availability

### Database Compatibility Notes

For future development, remember:
- Always use single quotes for string literals in raw SQL
- Use explicit boolean values (`true`/`false`) instead of integers
- Test queries with PostgreSQL syntax
- Use `ILIKE` for case-insensitive searches in PostgreSQL

The module is now fully compatible with PostgreSQL and ready for use! 🎉