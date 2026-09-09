# Project Brief and Rules

This is a standard laravel project for handling Point of Sale with inventory system. This markdown file located inside `/project/prompts/the_prompt.md` so you have too backward to go to the project root. You mostly may have to track every prompts start from `../routes/*` files.

Before writing every codes, you could check one of the controllers located at `app/Http/Controllers/*` to imitate my coding styles and how i handle the data.

Any storage need, you should use `public/storage/` which is a symbolic link from `storage/app/public`, make sure there is no other locations used.

When you need write a blade view, you can use any standard tailwindcss classes, i have set it up by using CDN that prepared from `resources/views/layouts/*`.