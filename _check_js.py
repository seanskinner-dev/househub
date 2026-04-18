import re

lines = open(
    r"C:\Users\Owner\househub-1\resources\views\points\index.blade.php",
    encoding="utf-8",
).read().splitlines()
block = "\n".join(lines[532:716])
# strip single-quoted strings
s = re.sub(r"'(?:\\.|[^'\\])*'", "''", block)
s = re.sub(r'"(?:\\.|[^"\\])*"', '""', s)
s = re.sub(r"/(?:\\.|[^/\\])+/[gimsuy]*", "R", s)
bal = 0
for i, ch in enumerate(s):
    if ch == "(":
        bal += 1
    elif ch == ")":
        bal -= 1
        if bal < 0:
            print("negative balance at index", i)
            print(s[max(0, i - 60) : i + 60])
            break
else:
    print("paren balance:", bal)
