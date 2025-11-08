# Alert Radius Implementation

## Overview
Users now only receive alerts for disasters and earthquakes that occur within their specified radius preference.

## What Was Changed

### 1. Disaster Alerts (DisasterManagementController.php)
- **Updated Method**: `createAlertsForDisaster()`
- **New Logic**: 
  - Retrieves each user's location preferences (latitude, longitude, radius_km)
  - Calculates distance between user location and disaster location using Haversine formula
  - Only creates alerts for users within their specified radius
  - Default radius: 100km (if user has no preferences set)
  - Default location: Manila (14.5995, 120.9842)

### 2. Earthquake Alerts (EarthquakeService.php)
- **Updated Method**: `checkAndCreateAlerts()`
- **New Logic**:
  - Checks all users and their preferences
  - Filters earthquakes by BOTH radius AND minimum magnitude
  - Calculates distance from user location to earthquake epicenter
  - Only creates alerts if:
    - Distance ≤ user's radius_km preference
    - Magnitude ≥ user's min_magnitude preference
  - Prevents duplicate alerts for the same earthquake
  - Assigns severity based on magnitude:
    - Critical: ≥ 7.0
    - High: ≥ 6.0
    - Moderate: ≥ 4.5
    - Low: < 4.5

### 3. Distance Calculation
- **Formula**: Haversine formula
- **Returns**: Distance in kilometers
- **Accuracy**: Accounts for Earth's curvature
- **Implementation**: Available in both DisasterManagementController and EarthquakeService

## User Settings

### Location Settings
- **Location Name**: Custom name for the location
- **Latitude**: User's latitude coordinate
- **Longitude**: User's longitude coordinate
- **Use Current Location**: Button to auto-detect GPS coordinates

### Alert Settings
- **Alert Radius**: 10-500 km (default: 100 km)
- **Minimum Magnitude**: 1.0-9.0 (default: 3.0) - for earthquakes only

## How It Works

### For Disasters:
1. Admin creates a disaster with location coordinates
2. System loops through all users
3. For each user:
   - Gets their location preferences (or uses Manila default)
   - Calculates distance from user to disaster
   - If distance ≤ radius_km → creates alert
   - If distance > radius_km → skips alert

### For Earthquakes:
1. System fetches earthquakes from USGS API (scheduled every 5 minutes)
2. For each earthquake:
   - Loops through all users
   - Gets user preferences (location, radius, min_magnitude)
   - Calculates distance from user to earthquake epicenter
   - If distance ≤ radius AND magnitude ≥ min_magnitude → creates alert
   - Otherwise → skips alert

## Testing

### Test Disaster Alerts:
1. Go to Settings and set your location and radius
2. Admin creates a disaster at a specific location
3. You should only receive alert if disaster is within your radius

### Test Earthquake Alerts:
1. Set your preferences (location, radius, min_magnitude)
2. Run: `php artisan earthquakes:fetch`
3. Check alerts - you should only see earthquakes within your radius and above your magnitude threshold

### Simulate Disaster:
1. Go to Admin Dashboard → Simulate Disaster
2. Check if alert appears based on your radius settings

## Default Values
If a user has no preferences set:
- **Location**: Manila, Philippines (14.5995, 120.9842)
- **Radius**: 100 km
- **Min Magnitude**: 3.0

## API Endpoints
- `GET /api/preferences` - Get user preferences
- `POST /api/preferences` - Update user preferences (uses PUT method internally)

## Benefits
✅ Users only get relevant alerts for their area
✅ Reduces alert fatigue
✅ Customizable alert radius per user
✅ Works for both disasters and earthquakes
✅ Respects magnitude preferences for earthquakes
✅ No duplicate alerts
