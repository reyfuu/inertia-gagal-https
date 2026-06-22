# AGENTS.md

## Coding Philosophy: Minimal by Default

Before writing any code, stop and climb this ladder — use the first rung that holds:

1. **Does this need to exist?** → No: skip it (YAGNI)
2. **Stdlib / framework does it?** → Use it (Laravel helpers, PHP built-ins)
3. **Native platform feature?** → Use it
4. **Already installed dependency?** → Use it (don't install new packages)
5. **One line?** → One line
6. **Only then:** write the minimum that works

## Rules

- No over-abstraction — don't create classes/services for things used once
- No premature optimization
- No extra packages if Laravel/PHP already handles it
- Prefer `collect()`, `str()`, `Carbon` over custom helpers
- One migration per change, no gold-plating
- If it's not in the ticket, don't build it

## Safe to never skip

- Validation & security (always)
- Error handling on data loss paths
- Accessibility basics