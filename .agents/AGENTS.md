# Workspace Rules — SR-Laravel

## Git & Deployment Control Rule

- **AUTOMATIC GIT OPERATIONS ARE STRICTLY PROHIBITED.**
- **DO NOT** execute any git staging, commit, push, pull, merge, or branch commands (`git add`, `git commit`, `git push`, `git pull`, `git checkout`, `git merge`, etc.).
- **Workflow**:
  1. Modify files normally.
  2. Save files and run local verification tests.
  3. STOP without running any git commands.
  4. Leave all modified files uncommitted so they are visible under VS Code / IDE **Source Control → Changes**.
- **Role Boundary**:
  - Code Editing & Verification = Agent
  - Git Staging, Commit, Push & Deployment = User
