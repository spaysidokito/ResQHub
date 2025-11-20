# Design Document

## Overview

This document outlines the technical design for migrating the ResQHub disaster monitoring application from Laravel (PHP) to Python. The migration will use **FastAPI** as the primary web framework due to its modern async capabilities, excellent performance, automatic API documentation, and strong typing support. The existing React frontend with Inertia.js will be preserved, and the database schema will remain unchanged.

### Technology Stack

**Backend:**
- **FastAPI** - Modern async web framework with automatic OpenAPI documentation
- **SQLAlchemy** - ORM for database operations (equivalent to Eloquent)
- **Alembic** - Database migration tool (equivalent to Laravel migrations)
- **Pydantic** - Data validation and serialization
- **Inertia-FastAPI** - Custom Inertia.js adapter for FastAPI
- **Passlib** - Password hashing with bcrypt support
- **APScheduler** - Task scheduling (equivalent to Laravel scheduler)
- **httpx** - Async HTTP client for external API calls
- **BeautifulSoup4** - Web scraping for PAGASA data
- **python-multipart** - File upload handling

**Frontend (Unchanged):**
- React 19
- Inertia.js
- TypeScript
- Tailwind CSS
- Vite

**Database:**
- MySQL or SQLite (existing schema preserved)

**Development Tools:**
- **pytest** - Testing framework
- **Black** - Code formatting
- **mypy** - Static type checking
- **uvicorn** - ASGI server for development

## Architecture

### High-Level Architecture

```
Frontend Layer (React + Inertia.js + TypeScript)
                    |
                    | HTTP/JSON
                    v
FastAPI Application (Routers, Middleware, Dependencies)
                    |
                    v
Service Layer (Disaster, Chatbot, External API Clients)
                    |
                    v
Data Layer (SQLAlchemy ORM, Models, Repository Pattern)
                    |
                    v
Database (MySQL/SQLite - Existing Schema Preserved)
```

### Directory Structure

```
resqhub-python/
├── app/
│   ├── __init__.py
│   ├── main.py                    # FastAPI application entry point
│   ├── config.py                  # Configuration management
│   ├── database.py                # Database connection and session
│   │
│   ├── models/                    # SQLAlchemy models
│   │   ├── __init__.py
│   │   ├── user.py
│   │   ├── disaster.py
│   │   ├── alert.py
│   │   └── ...
│   │
│   ├── schemas/                   # Pydantic schemas for validation
│   │   ├── __init__.py
│   │   ├── user.py
│   │   └── ...
│   │
│   ├── routers/                   # API route handlers
│   │   ├── __init__.py
│   │   ├── auth.py
│   │   ├── disasters.py
│   │   └── admin/
│   │
│   ├── services/                  # Business logic services
│   │   ├── __init__.py
│   │   ├── disaster_service.py
│   │   └── ...
│   │
│   ├── middleware/                # Custom middleware
│   ├── dependencies/              # FastAPI dependencies
│   ├── utils/                     # Utility functions
│   └── inertia/                   # Inertia.js adapter
│
├── alembic/                       # Database migrations
├── cli/                           # CLI commands
├── tests/                         # Test suite
├── public/                        # Static files
├── resources/                     # Frontend assets
└── storage/                       # File uploads
```

## Components and Interfaces

### 1. Database Models (SQLAlchemy)

All models will use SQLAlchemy ORM with declarative base, matching the existing Laravel schema exactly.

**Key Models:**
- User: Authentication, roles, gamification profile
- Disaster: Multi-hazard disaster tracking
- Earthquake: Seismic event data
- Alert: User notifications for disasters
- CitizenReport: User-submitted disaster reports
- Team: Collaborative groups
- Activity: Team activities and challenges
- Badge: Gamification achievements

### 2. Inertia.js Adapter

A custom Inertia adapter for FastAPI will handle:
- Response formatting for Inertia requests
- Shared data injection
- Asset versioning
- Partial reloads

### 3. Authentication System

Session-based authentication using:
- Passlib with bcrypt (Laravel compatible)
- Starlette SessionMiddleware
- Remember token support
- Auth and admin middleware

### 4. External API Services

**DisasterService:**
- Fetches data from USGS, NASA EONET, GDACS
- Async HTTP requests using httpx
- Error handling and retry logic

**PAGASAScraperService:**
- Web scraping using BeautifulSoup4
- Typhoon tracking data extraction

**ChatbotService:**
- Keyword-based response system
- Safety tips and emergency information
- Optional AI integration (OpenAI/Gemini)

### 5. Alert Generation System

**AlertService:**
- Monitors new disasters
- Calculates user proximity using Haversine formula
- Creates alerts based on user preferences

### 6. Gamification System

Components:
- Point System: Earned/spent point tracking
- Badge System: Achievement awards
- Leaderboards: Team and individual rankings
- Task Verification: Admin approval workflow

### 7. File Upload Handling

- Handles citizen report photos
- File validation (type, size)
- Storage in public/uploads directory
- URL generation for serving files

### 8. Scheduled Tasks

APScheduler will handle background tasks:
- Fetch disasters every 15 minutes
- Generate alerts every 5 minutes
- Clean old disasters daily

### 9. API Routers

Router organization:
- `/api/v1/*` - Public API endpoints
- `/dashboard` - Main dashboard (Inertia)
- `/teams/*` - Team management
- `/admin/*` - Admin panel

## Data Models

### Core Relationships

```
User
├── ownedTeams (1:N)
├── teamMemberships (1:N)
├── achievements (1:N)
├── points (1:N)
└── alerts (1:N)

Disaster
├── alerts (1:N)
└── citizenReports (1:N)

Team
├── owner (N:1 User)
├── members (1:N TeamMember)
├── activities (1:N)
└── badges (1:N)
```

## Error Handling

### Exception Hierarchy

- ResQHubException: Base exception
- AuthenticationError: Authentication failed
- AuthorizationError: Insufficient permissions
- ValidationError: Data validation failed
- ExternalAPIError: External API request failed

### Global Exception Handler

FastAPI exception handlers for consistent error responses across the application.

## Testing Strategy

### Test Structure

```
tests/
├── conftest.py              # Pytest fixtures
├── test_models/
├── test_services/
├── test_routers/
└── test_utils/
```

### Testing Approach

**Unit Tests:**
- Model methods and properties
- Service business logic
- Utility functions

**Integration Tests:**
- API endpoint responses
- Database operations
- Authentication flow

**Test Database:**
- SQLite in-memory database
- Automatic setup and teardown
- Fixture data for consistency

### Test Coverage Goals

- Models: 90%+ coverage
- Services: 85%+ coverage
- Routers: 80%+ coverage
- Overall: 85%+ coverage

## Configuration Management

Environment variables managed through Pydantic Settings:
- Application settings
- Database connection
- External API URLs
- AI service keys
- File upload limits
- Session configuration

## Deployment Considerations

### Production Server

- Uvicorn with multiple workers
- Gunicorn as process manager
- Nginx as reverse proxy
- Supervisor for process monitoring

### Docker Support

Containerized deployment with Docker and docker-compose.

### Performance Optimization

- Async database queries
- Connection pooling
- Response caching
- CDN for static assets
- Database query optimization

## Migration Path

### Phase 1: Core Infrastructure
1. Set up FastAPI project structure
2. Implement database models with SQLAlchemy
3. Create Inertia.js adapter
4. Implement authentication system

### Phase 2: API Endpoints
1. Migrate public API routes
2. Migrate web routes with Inertia responses
3. Implement admin routes
4. Add request validation

### Phase 3: Services
1. Migrate external API services
2. Implement chatbot service
3. Create alert generation system
4. Add scheduled tasks

### Phase 4: Features
1. Migrate gamification system
2. Implement file upload handling
3. Add citizen reporting
4. Complete admin panel

### Phase 5: Testing & Deployment
1. Write comprehensive tests
2. Performance testing
3. Documentation
4. Deployment setup
