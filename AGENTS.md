## IMPORTANT

### Model Standards
- All models should use `$guarded = []` instead of `$fillable`
- This allows mass assignment for all attributes

### Migration Standards
- Migrations should not include foreign key constraints
- Do not use `constrained()` method
- Do not use `onDelete('cascade')` or similar cascade delete operations

### Artisan Commands
- Always use `artisan make:*` commands to create Laravel resources
- This ensures correct stubs and following Laravel conventions
- Examples: `artisan make:model`, `artisan make:migration`, `artisan make:controller`, etc.
