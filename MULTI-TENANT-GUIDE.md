# Multi-Tenant Organization System - Implementation Guide

## 🎯 Overview

The Generic Scoring System has been enhanced with a complete **multi-tenant organization system** with role-based access control (RBAC). Users can belong to multiple organizations, and events are scoped to organizations with proper authorization.

## 🏗️ Architecture

### Multi-Tenancy Model

- **Organizations**: Top-level entities that own events and resources
- **Users**: Can belong to multiple organizations with different roles
- **Events**: Belong to one organization, created by a user
- **Resources**: Contestants, Judges, Criteria, Rounds - all scoped to organizations

### Role System

#### Super Admin
- **Access**: Everything in the system
- **Capabilities**:
  - Create/manage all organizations
  - View/edit all events across organizations
  - Full CRUD on all resources
  - System-wide statistics

#### Organization Head
- Set via `head_user_id` on Organization model
- **Access**: Full admin rights for their organization
- **Capabilities**:
  - Manage organization settings
  - Add/remove members and assign roles
  - Create/edit/delete events in their organization
  - Manage all organization resources

#### Organization Admin
- Set via pivot role = 'admin' in `organization_user` table
- **Access**: Admin rights within the organization
- **Capabilities**:
  - Create/edit/delete events
  - Manage organization members
  - Full CRUD on contestants, judges, criteria, rounds

#### Organization Member
- Set via pivot role = 'member' in `organization_user` table  
- **Access**: View-only access
- **Capabilities**:
  - View events in their organization
  - View contestants, judges, and other resources
  - Cannot create or modify resources

## 📦 Database Structure

### New Tables

#### `organizations`
```sql
- id
- name
- description
- logo
- head_user_id (foreign key to users)
- is_active
- timestamps
```

#### `organization_user` (Pivot)
```sql
- id
- organization_id
- user_id
- role (enum: 'admin', 'member')
- timestamps
- unique(organization_id, user_id)
```

### Modified Tables

#### `users`
- Added: `is_super_admin` (boolean)

#### `events`
- Added: `organization_id` (foreign key)
- Added: `created_by_user_id` (foreign key)
- Changed: Removed `organizer_id`

#### `contestants`, `judges`, `criterias`, `rounds`
- Added: `organization_id` (foreign key) to all

## 🔐 Authorization Policies

All resources have comprehensive policies:

- **OrganizationPolicy**: Controls organization access
- **EventPolicy**: Manages event permissions
- **ContestantPolicy**: Contestant access control
- **JudgePolicy**: Judge management permissions
- **CriteriaPolicy**: Criteria access control
- **RoundPolicy**: Round management permissions

### Policy Rules

1. **Super Admins**: Can do everything
2. **Organization Heads/Admins**: Can manage resources in their organization
3. **Organization Members**: Can only view resources
4. **Non-members**: Cannot access organization resources

## 🎨 Filament Resources

### Organization Resource
- **Location**: `app/Filament/Resources/OrganizationResource.php`
- **Features**:
  - Organization CRUD
  - UsersRelationManager for member management
  - Filtered by user's organizations (non-super-admins)
  - Only super admins can create organizations

### Event Resource - Enhanced
- **Location**: `app/Filament/Resources/Events/EventResource.php`
- **New Features**:
  - Organization selection (filtered by user's organizations)
  - Auto-sets `created_by_user_id` on creation
  - 4 Relation Managers for inline management:
    - ContestantsRelationManager
    - JudgesRelationManager
    - CriteriasRelationManager
    - RoundsRelationManager

### Hidden Resources
These are now hidden from navigation (accessible only via Event relation managers):
- ContestantResource
- JudgeResource
- CriteriaResource
- RoundResource

## 📊 Dashboard Widgets

### StatsOverview
- Shows event/contestant/judge counts
- Super admins see system-wide stats
- Regular users see organization-scoped stats

### RecentEvents
- Table widget showing latest 5 events
- Auto-filtered by user's organization access
- Links to event edit pages

### MyOrganizations
- Table widget showing user's organizations
- Super admins see all organizations
- Regular users see only their organizations
- Shows member counts, event counts, status

## 🚀 Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- `organizations` table
- `organization_user` pivot table
- Adds `is_super_admin` to users
- Adds `organization_id` to events and related tables

### 2. Seed Sample Data

```bash
php artisan db:seed
```

This creates:
- 1 Super Admin
- 3 Regular Users
- 3 Organizations (CS Society, Student Council, Arts Club)
- 2 Sample Events (Quiz Bee, Beauty Pageant)
- Sample contestants, judges, rounds, and criteria

### 3. Login Credentials

**Super Admin:**
- Email: `rnd_admin@ue.edu.ph`
- Password: `password`

**Org Admin (CS Society):**
- Email: `johngab@ue.edu.ph`
- Password: `password`

**Org Admin (Student Council):**
- Email: `maria@ue.edu.ph`
- Password: `password`

**Org Admin (Arts Club):**
- Email: `pedro@ue.edu.ph`
- Password: `password`

## 🔄 Workflow Changes

### Creating an Event (Before)
1. Select organizer from all users
2. Create event
3. Add contestants/judges separately

### Creating an Event (Now)
1. Select organization (filtered by your orgs)
2. Event auto-assigns your user ID as creator
3. Event auto-assigns organization_id
4. Use relation managers to add:
   - Contestants (inline creation)
   - Judges (attach existing or create new)
   - Criteria (for pageants)
   - Rounds (for quiz bees)
5. All related resources auto-inherit organization_id

### Managing Organizations

**Super Admins:**
1. Navigate to Organizations
2. Create new organization
3. Set organization head
4. Add members via relation manager
5. Assign roles (admin/member)

**Organization Heads/Admins:**
1. Navigate to Organizations
2. View their organizations
3. Edit organization settings
4. Add/remove members
5. Manage member roles

## 🎯 Key Features

### Global Scopes
All models automatically filter by accessible organizations:
- Non-super-admins only see resources from their organizations
- Super admins see everything
- Implemented in model `booted()` methods

### Automatic Organization Assignment
When creating resources via relation managers:
- `organization_id` is auto-set from parent event
- No manual selection needed
- Ensures data consistency

### Multi-Organization Membership
Users can belong to multiple organizations:
- Different roles in different orgs
- Access aggregated across all orgs
- Clean separation of data

### RBAC Throughout
Every action checks:
1. Is user super admin? → Allow
2. Is user org head/admin? → Check org ownership
3. Is user member? → View only
4. Not a member? → Deny

## 📝 Code Examples

### Check if User is Admin of Organization

```php
if ($user->isAdminOfOrganization($organization)) {
    // User can manage this organization
}
```

### Get User's Accessible Organization IDs

```php
$orgIds = $user->accessibleOrganizationIds();
// Returns all org IDs for regular users
// Returns all org IDs in system for super admins
```

### Check Organization Membership

```php
if ($user->belongsToOrganization($organization)) {
    // User is a member
}
```

### Auto-Filter by Organization (Model)

```php
// This is automatic in all models:
protected static function booted(): void
{
    static::addGlobalScope('organization', function (Builder $builder) {
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $organizationIds = Auth::user()->accessibleOrganizationIds();
            $builder->whereIn('organization_id', $organizationIds);
        }
    });
}
```

## 🔍 Testing the System

### As Super Admin
1. Login with super admin credentials
2. Create organizations
3. Add members to organizations
4. Verify you can see ALL events and resources

### As Organization Admin
1. Login with org admin credentials
2. Create an event (select your organization)
3. Add contestants/judges via relation managers
4. Verify you can only see your organization's data
5. Try adding members to your organization

### As Organization Member
1. Login with member credentials
2. Verify you can VIEW events in your organization
3. Verify you CANNOT create/edit/delete
4. Verify you don't see other organizations' data

## 🎨 UI/UX Improvements

1. **Navigation Simplified**: Contestants, Judges, Criteria, Rounds hidden from sidebar
2. **Inline Management**: Everything managed within Event pages via relation managers
3. **Smart Filtering**: Organization dropdowns show only accessible orgs
4. **Dashboard Insights**: Widgets provide quick overview of your data
5. **Role Badges**: Clear visual indicators of user roles in tables

## 🚨 Important Notes

1. **Existing Data**: If you have existing events/contestants/judges, you'll need to:
   - Create organizations
   - Update `organization_id` on existing records
   - Or run `migrate:fresh --seed` for clean start

2. **Token-Based Scoring**: Public/judge links still work as before
   - No authentication required for token-based access
   - Organization system doesn't affect public scoring

3. **Backwards Compatibility**: The `organizer_id` field was removed from events
   - Use `created_by_user_id` instead
   - Update any custom code referencing `organizer_id`

## 📚 File Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── OrganizationResource.php (NEW)
│   │   ├── OrganizationResource/
│   │   │   ├── Pages/
│   │   │   ├── RelationManagers/
│   │   │   │   └── UsersRelationManager.php (NEW)
│   │   │   ├── Schemas/
│   │   │   │   └── OrganizationForm.php (NEW)
│   │   │   └── Tables/
│   │   │       └── OrganizationsTable.php (NEW)
│   │   ├── Events/
│   │   │   ├── RelationManagers/ (NEW)
│   │   │   │   ├── ContestantsRelationManager.php
│   │   │   │   ├── JudgesRelationManager.php
│   │   │   │   ├── CriteriasRelationManager.php
│   │   │   │   └── RoundsRelationManager.php
│   │   │   └── Schemas/
│   │   │       └── EventForm.php (UPDATED)
│   │   ├── Contestants/ (UPDATED - Hidden)
│   │   ├── Judges/ (UPDATED - Hidden)
│   │   ├── Criterias/ (UPDATED - Hidden)
│   │   └── Rounds/ (UPDATED - Hidden)
│   └── Widgets/ (NEW)
│       ├── StatsOverview.php
│       ├── RecentEvents.php
│       └── MyOrganizations.php
├── Models/
│   ├── Organization.php (NEW)
│   ├── User.php (UPDATED)
│   ├── Event.php (UPDATED)
│   ├── Contestant.php (UPDATED)
│   ├── Judge.php (UPDATED)
│   ├── Criteria.php (UPDATED)
│   └── Round.php (UPDATED)
└── Policies/ (ALL NEW)
    ├── OrganizationPolicy.php
    ├── EventPolicy.php
    ├── ContestantPolicy.php
    ├── JudgePolicy.php
    ├── CriteriaPolicy.php
    └── RoundPolicy.php

database/
└── migrations/ (NEW)
    ├── 2025_10_21_100001_create_organizations_table.php
    ├── 2025_10_21_100002_create_organization_user_table.php
    └── 2025_10_21_100003_add_organization_and_role_fields.php
```

## ✅ Checklist

- [x] Organizations table and model
- [x] User-Organization many-to-many relationship
- [x] Role system (super_admin, org admin, member)
- [x] Organization resource in Filament
- [x] Event resource updates (organization_id)
- [x] Relation managers for Event (contestants, judges, criteria, rounds)
- [x] Hide standalone resources from navigation
- [x] Comprehensive policies for all models
- [x] Dashboard widgets (stats, recent events, organizations)
- [x] Database seeders with sample data
- [x] Global scopes for organization filtering
- [x] Documentation

## 🎉 Summary

The system now provides enterprise-level multi-tenancy with:
- Clean separation of data between organizations
- Flexible role-based access control
- Intuitive UI with relation managers
- Comprehensive authorization policies
- Dashboard insights
- Sample data for testing

All existing features (token-based scoring, public viewing, etc.) remain fully functional!
