# IT8415 Recipe Platform – Group 2

## Overview

The IT8415 Recipe Platform is a web-based application that enables users to discover, create, manage, and share recipes. The platform provides an intuitive user experience for browsing recipes, viewing detailed cooking instructions, and organizing personal recipe collections.

This project was developed as part of the IT8415 course by Group 2.

---

## Features

### User Features
- User registration and authentication
- Browse recipe catalog
- Search recipes by name, ingredients, or category
- View detailed recipe information
- Create and publish recipes
- Edit and delete personal recipes
- Save favorite recipes
- Responsive design for desktop and mobile devices

### Admin Features
- Manage users
- Manage recipe categories
- Moderate recipe content
- System administration dashboard

---

## Technology Stack

### Frontend
- [Add Frontend Framework]
- HTML5
- CSS3
- JavaScript

### Backend
- [Add Backend Framework]

### Database
- [Add Database]

### Other Tools
- Git & GitHub
- [Add additional tools]

---

## System Architecture

```
Client (Browser)
       |
       v
Frontend Application
       |
       v
Backend API
       |
       v
Database
```

---

## Project Structure

```
IT8415_RecipePlatform_Group2/
│
├── frontend/
│   ├── src/
│   ├── public/
│   └── components/
│
├── backend/
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   └── services/
│
├── database/
│
├── docs/
│
└── README.md
```

---

## Installation

### Prerequisites

Ensure you have installed:

- Git
- [Node.js / .NET / Java / Python]
- [Database System]

### Clone Repository

```bash
git clone https://github.com/alihaji647/IT8415_RecipePlatform_Group2.git
cd IT8415_RecipePlatform_Group2
```

### Install Dependencies

```bash
# Frontend
cd frontend
npm install

# Backend
cd ../backend
npm install
```

### Configure Environment Variables

Create a `.env` file:

```env
DB_HOST=localhost
DB_NAME=recipe_platform
DB_USER=username
DB_PASSWORD=password
JWT_SECRET=your_secret_key
```

### Run Application

Frontend:

```bash
npm start
```

Backend:

```bash
npm run dev
```

---

## Usage

1. Register a new account.
2. Log in to the platform.
3. Browse available recipes.
4. Create and manage your own recipes.
5. Save favorite recipes for future access.

---

## Database Design

### Main Entities

- Users
- Recipes
- Categories
- Ingredients
- Favorites
- Reviews

### Relationships

- One User → Many Recipes
- One Recipe → Many Ingredients
- One User → Many Favorites
- One Category → Many Recipes

---

## Testing

Run tests:

```bash
npm test
```

or

```bash
dotnet test
```

depending on the project technology stack.

---

## Team Members

### Group 2

- Ali Haji
- Hasan Janahi
- Yusuf Naser
- Ali Sarhan
- Rashed Alsowaidi

---

## Course Information

**Course:** IT8415  
**Project:** Recipe Platform  
**Institution:** [University Name]  
**Semester:** [Semester/Year]

---

## Future Enhancements

- Recipe rating system
- Comment functionality
- Meal planning tools
- Shopping list generation
- AI-powered recipe recommendations
- Social sharing features

---

## License

This project was developed for educational purposes as part of the IT8415 course.

---

## Repository

:contentReference[oaicite:0]{index=0}
