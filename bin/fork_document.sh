#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

print_usage() {
    echo "Usage: $0 <path_to_standard_file>" >&2
    echo "The argument must be one of the four artifacts of a standard in the 'standards' directory:" >&2
    echo "  - standards/<Name>S.odt  (or .pdf)" >&2
    echo "  - standards/<Name>TN.odt (or .pdf)" >&2
    echo "  - standards/generated/<Name>S.txt" >&2
    echo "  - standards/generated/<Name>TN.txt" >&2
}

# 1. Verify exactly one argument is provided
if [ "$#" -ne 1 ]; then
    print_usage
    exit 1
fi

# 2. Check if the file exists and is a regular file
ABS_PATH=$(realpath "$1" 2>/dev/null || true)
if [ -z "$ABS_PATH" ] || [ ! -f "$ABS_PATH" ]; then
    echo "Error: File '$1' does not exist or is not a regular file." >&2
    print_usage
    exit 1
fi

# 3. Locate the git repository root relative to the file
FILE_DIR=$(dirname "$ABS_PATH")
REPO_ROOT=$(git -C "$FILE_DIR" rev-parse --show-toplevel 2>/dev/null || true)
if [ -z "$REPO_ROOT" ]; then
    echo "Error: File '$1' is not inside a git repository." >&2
    print_usage
    exit 1
fi

# Change directory to the repository root for start-directory resilience
cd "$REPO_ROOT"

# 4. Verify the file path is under standards/
RELATIVE_PATH=$(realpath --relative-to="$REPO_ROOT" "$ABS_PATH" 2>/dev/null || true)
if [[ "$RELATIVE_PATH" != standards/* ]]; then
    echo "Error: File '$1' is not in the 'standards' directory of this repository." >&2
    print_usage
    exit 1
fi

# 5. Verify the file extension and directory constraints
FILENAME=$(basename "$ABS_PATH")
EXT="${FILENAME##*.}"
if [[ "$EXT" != "odt" && "$EXT" != "pdf" && "$EXT" != "txt" ]]; then
    echo "Error: File has invalid extension '$EXT'. Must be .odt, .pdf, or .txt." >&2
    print_usage
    exit 1
fi

if [[ "$EXT" == "txt" ]]; then
    if [[ "$RELATIVE_PATH" != standards/generated/* ]]; then
        echo "Error: Text files (.txt) must be in 'standards/generated/'." >&2
        print_usage
        exit 1
    fi
else
    if [ "$(dirname "$RELATIVE_PATH")" != "standards" ]; then
        echo "Error: Document files (.odt, .pdf) must be directly in the 'standards/' directory." >&2
        print_usage
        exit 1
    fi
fi

# 6. Extract the document's base name and ensure it ends with S or TN
WITHOUT_EXT="${FILENAME%.*}"
if [[ "$WITHOUT_EXT" == *S ]]; then
    BASE="${WITHOUT_EXT%S}"
elif [[ "$WITHOUT_EXT" == *TN ]]; then
    BASE="${WITHOUT_EXT%TN}"
else
    echo "Error: File name '$FILENAME' must end with 'S' or 'TN' (excluding extension)." >&2
    print_usage
    exit 1
fi

# 7. Check preconditions: repository is clean (no uncommitted edits on tracked files)
if [ -n "$(git status --porcelain -uno)" ]; then
    echo "Error: Repository has pending edits (uncommitted changes)." >&2
    git status -s -uno >&2
    exit 1
fi

# 8. Check preconditions: master branch is checked out
CURRENT_BRANCH=$(git symbolic-ref --short -q HEAD || true)
if [ "$CURRENT_BRANCH" != "master" ]; then
    echo "Error: The 'master' branch must be checked out. Current branch is '${CURRENT_BRANCH:-(detached HEAD)}'." >&2
    exit 1
fi

# 9. Check preconditions: master is up to date with remote master (no pending fast-forward merges)
UPSTREAM=$(git rev-parse --abbrev-ref @{u} 2>/dev/null || true)
if [ -z "$UPSTREAM" ]; then
    echo "Error: No upstream tracking branch configured for 'master'." >&2
    exit 1
fi

BEHIND_COUNT=$(git rev-list --count HEAD.."$UPSTREAM" 2>/dev/null || true)
if [ -z "$BEHIND_COUNT" ]; then
    echo "Error: Could not check upstream commit status." >&2
    exit 1
fi

if [ "$BEHIND_COUNT" -gt 0 ]; then
    echo "Error: Local branch 'master' is behind '$UPSTREAM' by $BEHIND_COUNT commit(s)." >&2
    echo "There are pending fast-forward merges. Please merge/pull first." >&2
    exit 1
fi

# 10. Prepare copying configurations
declare -A FILES_TO_COPY
FILES_TO_COPY["standards/${BASE}S.odt"]="drafts/${BASE}S.odt"
FILES_TO_COPY["standards/${BASE}S.pdf"]="drafts/${BASE}S.pdf"
FILES_TO_COPY["standards/generated/${BASE}S.txt"]="drafts/generated/${BASE}S.txt"
FILES_TO_COPY["standards/${BASE}TN.odt"]="drafts/${BASE}TN.odt"
FILES_TO_COPY["standards/${BASE}TN.pdf"]="drafts/${BASE}TN.pdf"
FILES_TO_COPY["standards/generated/${BASE}TN.txt"]="drafts/generated/${BASE}TN.txt"

# 11. Check if any destination file already exists to prevent partial/overwriting runs
for dst in "${FILES_TO_COPY[@]}"; do
    if [ -e "$dst" ]; then
        echo "Error: Destination file '$dst' already exists in drafts. Aborting." >&2
        exit 1
    fi
done

# 12. Verify the copy helper bin/git-cp is available and executable
if [ ! -x "bin/git-cp" ]; then
    echo "Error: bin/git-cp is not found or not executable." >&2
    exit 1
fi

# 13. Perform the fork with history for each existing source artifact
ANY_COPIED=false
ORDER=(
    "standards/${BASE}S.odt"
    "standards/${BASE}S.pdf"
    "standards/generated/${BASE}S.txt"
    "standards/${BASE}TN.odt"
    "standards/${BASE}TN.pdf"
    "standards/generated/${BASE}TN.txt"
)

for src in "${ORDER[@]}"; do
    dst="${FILES_TO_COPY[$src]}"
    if [ -f "$src" ]; then
        echo "Forking $src to $dst..."
        bin/git-cp "$src" "$dst"
        ANY_COPIED=true
    fi
done

if [ "$ANY_COPIED" = false ]; then
    echo "Error: No matching standard files found to fork for '$BASE'." >&2
    exit 1
fi

echo "Fork process complete for all artifacts of standard '$BASE'."
