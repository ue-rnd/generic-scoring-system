# Tailwind CSS Integration Guide

## 📚 Official Filament Guidance

This project uses **Tailwind CSS v4** with **Filament v4**. Understanding how they work together is crucial.

---

## ✅ Inside Filament Admin Panel

### Pages inside `/app/Filament/` directory

**You can use Tailwind classes freely WITHOUT any additional setup:**

```blade
<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::card>
            <!-- Tailwind classes work automatically -->
        </x-filament::card>
    </div>
</x-filament-panels::page>
```

**Why it works:**
- Filament already includes and compiles Tailwind CSS
- All Tailwind utility classes are available
- Filament components are pre-styled with Tailwind

**❌ NEVER add Tailwind CDN in Filament views:**
```blade
<!-- ❌ WRONG - Causes conflicts and breaks styling -->
<script src="https://cdn.tailwindcss.com"></script>
```

---

## ✅ Outside Filament (Public Pages)

### Judge Scoring, Public Viewing, Auth Pages

**Use the compiled Vite assets:**

```blade
<!DOCTYPE html>
<html>
<head>
    <title>My Page</title>
    <!-- ✅ CORRECT - Use Vite to load compiled Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Now Tailwind classes work -->
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">Hello World</h1>
    </div>
</body>
</html>
```

**Why this approach:**
- Compiles Tailwind v4 from your config
- Optimizes and purges unused CSS
- Works in development and production
- Consistent styling across your app

---

## 🛠️ Project Configuration

### 1. **Tailwind CSS v4 Setup** (`resources/css/app.css`)

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}
```

**What this does:**
- Imports Tailwind v4
- Scans all Blade files for Tailwind classes
- Purges unused CSS in production
- Defines custom theme tokens

### 2. **Vite Configuration** (`vite.config.js`)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

**What this does:**
- Integrates Tailwind v4 with Vite
- Compiles CSS automatically
- Enables hot module reloading

---

## 🚀 Development Workflow

### **Option 1: Development Mode (Recommended)**

```bash
npm run dev
```

**Benefits:**
- Hot reload (instant updates)
- Fast compilation
- Development-friendly error messages

### **Option 2: Build for Production**

```bash
npm run build
```

**Benefits:**
- Optimized and minified CSS
- Purges unused classes
- Smallest file size

---

## 📁 File Structure

```
├── resources/
│   ├── css/
│   │   └── app.css                    # Tailwind v4 entry point
│   ├── js/
│   │   └── app.js                     # JavaScript entry point
│   └── views/
│       ├── filament/                  # Admin panel views (Tailwind included)
│       │   └── resources/
│       │       └── events/
│       │           └── pages/
│       │               └── manage-event-access.blade.php
│       ├── scoring/                   # Public pages (use @vite)
│       │   ├── judge.blade.php
│       │   └── results.blade.php
│       ├── public/                    # Public pages (use @vite)
│       │   └── event.blade.php
│       └── auth/                      # Auth pages (use @vite)
│           └── login.blade.php
├── public/
│   └── build/                         # Compiled assets (generated)
│       ├── manifest.json
│       └── assets/
│           ├── app-[hash].css
│           └── app-[hash].js
└── vite.config.js                     # Vite configuration
```

---

## ✅ Correct Examples

### Filament Admin Panel Page
```blade
<x-filament-panels::page>
    <!-- ✅ Tailwind classes work automatically -->
    <div class="grid grid-cols-3 gap-6">
        <x-filament::card>
            <x-filament::section.heading>
                Total Judges: 5
            </x-filament::section.heading>
        </x-filament::card>
    </div>
</x-filament-panels::page>
```

### Public Judge Scoring Page
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Judge Scoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- ✅ Tailwind classes work -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold">Score Event</h1>
    </div>
</body>
</html>
```

---

## ❌ Common Mistakes

### 1. Using Tailwind CDN in Filament views
```blade
<!-- ❌ WRONG -->
<x-filament-panels::page>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- This breaks Filament's styling -->
</x-filament-panels::page>
```

**Why it's wrong:**
- Loads a different Tailwind version
- Conflicts with Filament's compiled CSS
- Breaks dark mode and theme colors

### 2. Using Tailwind CDN in public pages
```blade
<!-- ❌ WRONG -->
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Use @vite instead -->
```

**Why it's wrong:**
- Loads entire Tailwind library (huge file size)
- No purging of unused CSS
- Slower page loads
- No custom theme configuration

### 3. Forgetting @vite in public pages
```blade
<!-- ❌ WRONG -->
<!DOCTYPE html>
<html>
<head>
    <!-- Missing @vite directive -->
</head>
<body>
    <div class="container mx-auto">
        <!-- Tailwind classes won't work -->
    </div>
</body>
</html>
```

---

## 🎯 Summary

| Context | Method | CDN? | @vite? |
|---------|--------|------|--------|
| Filament Admin Panel | Use Tailwind classes directly | ❌ No | ❌ No |
| Public Pages (judge, results) | Use @vite directive | ❌ No | ✅ Yes |
| Auth Pages (login) | Use @vite directive | ❌ No | ✅ Yes |
| Welcome Page | Use @vite directive | ❌ No | ✅ Yes |

---

## 🔧 Troubleshooting

### **Tailwind classes not working in public pages?**

1. Check if `@vite` directive is present:
   ```blade
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   ```

2. Build the assets:
   ```bash
   npm run build
   ```

3. Clear view cache:
   ```bash
   php artisan view:clear
   ```

### **Filament styling broken?**

1. Remove any Tailwind CDN scripts from Filament views
2. Clear browser cache
3. Hard refresh (Cmd/Ctrl + Shift + R)

### **Development server not hot reloading?**

1. Make sure dev server is running:
   ```bash
   npm run dev
   ```

2. Check if Vite is accessible at `http://localhost:5173`

---

## 📚 References

- [Tailwind CSS v4 Documentation](https://tailwindcss.com/docs)
- [Filament v4 Documentation](https://filamentphp.com/docs)
- [Laravel Vite Documentation](https://laravel.com/docs/vite)
- [Vite Documentation](https://vite.dev)

---

## ✅ All Fixed Files

The following files have been updated to use proper Tailwind integration:

1. ✅ `resources/views/scoring/judge.blade.php` - Changed from CDN to @vite
2. ✅ `resources/views/scoring/results.blade.php` - Changed from CDN to @vite
3. ✅ `resources/views/public/event.blade.php` - Changed from CDN to @vite
4. ✅ `resources/views/auth/login.blade.php` - Changed from CDN to @vite
5. ✅ `resources/views/filament/resources/events/pages/manage-event-access.blade.php` - Already correct (no @vite needed)

**All pages now use proper Tailwind integration! 🎉**
