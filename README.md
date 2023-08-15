# Blog Website Using Symfony Framework

This website offers a platform for users to create, share, and interact with blog posts. The website is equipped with various useful features.

## Features

### Authentication and User Management

1. **Register + Login (Security):** Users can securely register and log in to the website.
2. **Welcome User Text and Logout Link:** The website displays a personalized welcome message along with a logout link in the header of all pages using Twig template hierarchy and the 'extends' keyword.

### Blog Post Management

3. **Add a Blog Post:** Authenticated users can add new blog posts, and each post is linked to the user who created it using a ManyToOne relationship.
4. **Display Full Content:** Visitors can view the full content of a blog post.
5. **Count Views:** The website keeps track of the number of views each blog post receives.
6. **List All Blog Posts:** The website shows a list of all blog posts, including author names and view counts.

### User Interaction

1. **Like a Blog Post:** Users can like blog posts using JavaScript.
2. **List Blog Posts by User:** The website provides a list of blog posts by a specific user.
3. **Generate Slugs:** Each blog post is assigned a slug to enhance SEO and user-friendliness.
4. **Author Management:** Authors can manage their blog posts, including editing, moving to trash, and emptying the trash.
5. **Show 404 Not Found Page:** When accessing a removed blog post, the website shows a custom 404 not found page.

### Content Enhancement

1. **Custom HTML Editor Form Type:** A custom form type allows users to use an HTML editor for composing blog content.
2. **Reply to Comments:** Users have the ability to reply to comments on blog posts.
