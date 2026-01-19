# GitHub Copilot Instructions

This directory contains path-specific instructions for GitHub Copilot coding agent. These instructions complement the main `.github/copilot-instructions.md` file by providing targeted guidance for specific file types and areas of the codebase.

## How It Works

When GitHub Copilot works on a file, it automatically applies instructions based on the file's path. Instructions are matched using glob patterns defined in the YAML front matter of each file.

## Instruction Files

### General Development

- **`php-development.instructions.md`** (`**/*.php`)
  - PHP development standards
  - Path management patterns
  - Security requirements (SQL injection, XSS, CSRF)
  - Authentication checks
  - Code style guidelines
  - Error handling patterns

- **`css-design.instructions.md`** (`**/*.css`)
  - Material Design 3 Dark theme tokens
  - Design system guidelines
  - Component patterns
  - Responsive design standards
  - Performance best practices

### Area-Specific Instructions

- **`authentication.instructions.md`** (`**/auth/**/*.php`)
  - Password hashing with bcrypt
  - Session management
  - Email verification flow
  - Password reset implementation
  - Remember me functionality
  - Rate limiting

- **`user-area.instructions.md`** (`**/user/**/*.php`)
  - User dashboard implementation
  - Profile management
  - Avatar uploads
  - Account settings
  - Download history
  - User preferences

- **`download-portal.instructions.md`** (`**/files/**/*.php`)
  - Secure file downloads
  - Download logging and analytics
  - Category and search functionality
  - Pagination patterns
  - File type validation

- **`admin-panel.instructions.md`** (`**/admin/**/*.php`)
  - Admin authorization checks
  - User management (CRUD operations)
  - Download management with file uploads
  - Comment moderation
  - Bulk actions
  - Activity logging

## Hierarchy

Instructions are applied in the following order:

1. **Path-specific instructions** (this directory) - Most specific
2. **Repository-wide instructions** (`.github/copilot-instructions.md`)
3. **Global agent instructions** (`AGENTS.md`, `CLAUDE.md`)
4. **Organization-wide instructions** (if configured)

More specific instructions take precedence over general ones.

## Best Practices

### When to Add New Instructions

Create new path-specific instructions when:
- Working with a new section of the application
- Introducing new file types or patterns
- Establishing section-specific security requirements
- Documenting unique workflows for an area

### Writing Good Instructions

1. **Use specific glob patterns** - Target the right files
2. **Include examples** - Show, don't just tell
3. **Focus on what's unique** - Don't repeat general instructions
4. **Prioritize security** - Highlight critical security requirements
5. **Keep it actionable** - Instructions should be clear and implementable

### YAML Front Matter

Every instruction file must start with YAML front matter:

```yaml
---
applyTo: "**/*.php"
---
```

The `applyTo` field uses glob patterns to match file paths:
- `**/*.php` - All PHP files
- `**/admin/**/*.php` - PHP files in any admin directory
- `**/tests/*.spec.ts` - TypeScript spec files in tests directories

## Testing Instructions

To verify instructions are working:

1. Make changes to a file that matches a pattern
2. Check that Copilot suggestions align with the instructions
3. Review pull requests to ensure guidelines are followed
4. Update instructions based on feedback and evolution

## Maintenance

### Regular Updates

- Review instructions quarterly or when major changes occur
- Update based on code review feedback
- Add new patterns as the codebase evolves
- Remove outdated or superseded instructions

### Version Control

- All instruction files are version controlled
- Changes go through pull request review
- Document significant changes in commit messages

## Additional Resources

- [GitHub Copilot Best Practices](https://gh.io/copilot-coding-agent-tips)
- [Repository-wide Instructions](../copilot-instructions.md)
- [Agent Orchestration Guide](../../AGENTS.md)
- [Development Guide](../../CLAUDE.md)

## Questions?

For questions about instructions or to suggest improvements:
- Open an issue on GitHub
- Contact via WhatsApp: +49-152-23842897
- Email: info@babixgo.de
