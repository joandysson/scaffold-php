import os
import sys
import json
import xml.etree.ElementTree as ET
import subprocess

def main():
    if len(sys.argv) < 3:
        print("Usage: coverage_summary.py <coverage.xml> <base_ref>", file=sys.stderr)
        sys.exit(1)

    cov_file = sys.argv[1]
    base_ref = sys.argv[2]

    tree = ET.parse(cov_file)
    root = tree.getroot()

    project = root.find('project')
    metrics = project.find('metrics')
    total_statements = int(metrics.get('statements', 0))
    covered_statements = int(metrics.get('coveredstatements', 0))
    total = round(100 * covered_statements / total_statements, 2) if total_statements else 0.0

    changed_files = subprocess.check_output(['git', 'diff', '--name-only', base_ref]).decode().splitlines()
    changed_files = [os.path.abspath(f) for f in changed_files if f.endswith('.php')]

    changed_total = 0
    changed_covered = 0
    for file_el in project.findall('.//file'):
        file_path = os.path.abspath(file_el.get('name'))
        if file_path in changed_files:
            m = file_el.find('metrics')
            if m is not None:
                changed_total += int(m.get('statements', 0))
                changed_covered += int(m.get('coveredstatements', 0))

    changed = round(100 * changed_covered / changed_total, 2) if changed_total else 0.0

    summary = {"total": total, "changed": changed}
    print(json.dumps(summary))

if __name__ == '__main__':
    main()
