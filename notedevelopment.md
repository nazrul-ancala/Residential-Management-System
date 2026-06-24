# Developer Tools Notes

Quick reference for AI productivity tools used in this project.

---

## 1. Caveman — Token Compression

Compresses Claude Code responses ~65-75%. Same technical accuracy, less fluff.
Repo: https://github.com/JuliusBrussee/caveman

### Install (Windows, one-time global)

> Run from home directory `~`, NOT `C:\Windows\System32`

```powershell
cd ~
$tmp = "$env:TEMP\caveman_install.ps1"
Invoke-WebRequest -Uri "https://raw.githubusercontent.com/JuliusBrussee/caveman/main/install.ps1" -OutFile $tmp -UseBasicParsing
& powershell -ExecutionPolicy Bypass -File $tmp
```

### Wire into Claude Code (one-time after install)

The installer doesn't auto-detect Claude Code. Copy skills manually:

```powershell
New-Item -ItemType Directory -Path "$env:USERPROFILE\.claude\commands" -Force

$src = "$env:USERPROFILE\.agents\skills"
$dst = "$env:USERPROFILE\.claude\commands"
Get-ChildItem $src | ForEach-Object {
    Copy-Item "$($_.FullName)\SKILL.md" "$dst\$($_.Name).md"
}
```

Restart Claude Code after this.

### Skills installed

| File | Slash Command | What it does |
|---|---|---|
| caveman.md | `/caveman` | Enable compressed mode |
| caveman-commit.md | `/caveman-commit` | Short conventional commit messages |
| caveman-compress.md | `/caveman-compress <file>` | Compress memory/context files |
| caveman-help.md | `/caveman-help` | Show all commands |
| caveman-review.md | `/caveman-review` | One-line PR review comments |
| caveman-stats.md | `/caveman-stats` | Token savings this session |
| cavecrew.md | `/cavecrew` | Subagent delegation guide |

### Usage

```
/caveman          → full mode (default)
/caveman lite     → keep articles, full sentences, just no filler
/caveman ultra    → maximum compression, arrows for causality
/caveman-stats    → see how many tokens saved
stop caveman      → back to normal mode
```

**Intensity levels:**

| Level | Style |
|---|---|
| lite | Professional and tight. Articles kept. |
| full | Classic caveman. Fragments OK. No articles. |
| ultra | Abbreviate prose (`req/res/fn/impl`). Arrows (`→`). |
| wenyan-full | Classical Chinese 文言文 style. |

**Auto-disables for:** security warnings, destructive operations, ambiguous multi-step sequences.

**Persists** for whole session. Only stops on `stop caveman` or `normal mode`.

---

## 2. Codebase Memory MCP — Knowledge Graph

Parses your codebase into a searchable graph. 158 languages. Answers "what calls X?"
in <1ms. Reduces Claude token usage 99.2% vs file-by-file search.
Repo: https://github.com/DeusData/codebase-memory-mcp

### Install (Windows, one-time global)

```powershell
# Download zip (exe not available, use zip)
curl.exe -L "https://github.com/DeusData/codebase-memory-mcp/releases/download/v0.8.1/codebase-memory-mcp-windows-amd64.zip" -o "$env:TEMP\codebase-memory-mcp.zip"

# Extract
Expand-Archive "$env:TEMP\codebase-memory-mcp.zip" -DestinationPath "$env:TEMP\codebase-memory-mcp" -Force

# Install binary to PATH
& powershell -ExecutionPolicy Bypass -File "$env:TEMP\codebase-memory-mcp\install.ps1"

# Configure all detected agents (Claude Code, Cursor, VS Code, etc.)
codebase-memory-mcp install
```

Restart Claude Code after this.

### What gets configured automatically

- Claude Code: `~/.claude/.mcp.json` + hooks (PreToolUse search augmenter, SessionStart reminder)
- Cursor: `~/.cursor/mcp.json`
- VS Code: `~/AppData/Roaming/Code/User/mcp.json`
- Codex CLI: `~/.codex/config.toml`
- Gemini CLI: `~/.gemini/settings.json`

### Per-project setup (index first)

Must index before any graph queries. Run once per project, re-run after big changes.

```
Tell Claude: "index this repo at D:\path\to\project"
```

Or manually via MCP tool:
- Tool: `index_repository`
- `repo_path`: path to project root
- `mode`: `moderate` (recommended) / `fast` / `full`

**Indexing modes:**

| Mode | Speed | Use when |
|---|---|---|
| fast | Fastest | Quick check, skip similarity edges |
| moderate | Medium | Normal use (recommended) |
| full | Slowest | Deep analysis, semantic similarity |

### MCP Tools available in Claude Code

| Tool | What it does |
|---|---|
| `index_repository` | Build/refresh the knowledge graph |
| `get_architecture` | High-level overview: packages, clusters, hotspots, routes |
| `search_graph` | Find functions/classes by name or pattern |
| `trace_path` | Trace call chain from one function to another |
| `get_code_snippet` | Get exact source for a qualified symbol name |
| `search_code` | Graph-augmented text search |
| `query_graph` | Custom Cypher queries for complex patterns |
| `detect_changes` | What changed since last index |
| `index_status` | Check if project is indexed and fresh |
| `list_projects` | See all indexed projects |
| `delete_project` | Remove a project from the graph |

### Useful prompts

```
"give me architecture overview of this project"
"what calls the function saveServiceOrder?"
"what would break if I change EmployeeController?"
"find all dead code"
"trace the call chain for ValidateHeaderSecurity"
"what files import the Customer model?"
"show me all HTTP routes"
```

### Team sharing (optional)

Export compressed snapshot so teammates skip re-indexing:

```
index_repository → persistence: true
```

Saves `.codebase-memory/graph.db.zst` — commit to repo.
Teammates get instant graph on first open.

---

## Combined Workflow for New Project

```
1. Open Claude Code in project folder
2. /caveman                        ← activate compression
3. "index this repo"               ← build knowledge graph
4. "give me architecture overview" ← understand the codebase
5. Start asking questions / making changes
```

---

## Troubleshooting

### Caveman `/caveman` not recognized
- Check `~/.claude/commands/caveman.md` exists
- If not, re-run the wire-into-Claude-Code steps above
- Restart Claude Code

### Codebase MCP tools not showing
- Run `codebase-memory-mcp install` in terminal
- Restart Claude Code
- Check `~/.claude/.mcp.json` has the server entry

### Download fails on PowerShell 5.1
```powershell
# Add TLS fix before Invoke-WebRequest
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
```

### Installer runs from wrong directory
- Always `cd ~` first before running install scripts
- Running from `C:\Windows\System32` causes EPERM errors
