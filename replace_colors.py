import os
import re

directory = '/Users/chikku/Desktop/Projects/Code/fullstack/ecom-billing/resources/views'

replacements = {
    r'bg-red-50(?![\w/-])': 'bg-blue-50',
    r'bg-red-100(?![\w/-])': 'bg-blue-100',
    r'bg-red-500/5': 'bg-blue-500/5',
    r'bg-red-500/10': 'bg-blue-500/10',
    r'bg-red-500/15': 'bg-blue-500/15',
    r'bg-red-500/20': 'bg-blue-500/20',
    r'bg-red-600/5': 'bg-blue-600/5',
    r'bg-red-650/5': 'bg-blue-500/5',
    r'bg-red-950/20': 'bg-blue-950/20',
    r'border-red-100': 'border-blue-100',
    r'border-red-150': 'border-blue-150',
    r'border-red-500/15': 'border-blue-500/15',
    r'border-red-500/20': 'border-blue-500/20',
    r'border-red-900/40': 'border-blue-900/40',
    r'border-red-900/50': 'border-blue-900/50',
    r'text-red-405': 'text-blue-400',
    r'text-red-400': 'text-blue-400',
    r'hover:text-red-500': 'hover:text-blue-500',
    r'dark:hover:text-red-400': 'dark:hover:text-blue-400',
    r'dark:hover:bg-red-500': 'dark:hover:bg-blue-600',
    r'hover:bg-red-500': 'hover:bg-blue-600',
    r'shadow-red-500/5': 'shadow-blue-500/5',
    r'shadow-red-500/10': 'shadow-blue-500/10',
    r'shadow-red-500/20': 'shadow-blue-500/20',
    r'shadow-red-600/15': 'shadow-blue-600/15',
    r'shadow-red-600/10': 'shadow-blue-600/10',
}

for root, dirs, files in os.walk(directory):
    if 'admin' in root:
        continue
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            orig = content
            for pattern, replacement in replacements.items():
                content = re.sub(pattern, replacement, content)
            
            if content != orig:
                print(f"Updating {filepath}")
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
print("Finished updates.")
