# Logo Settings Guide

## Overview
The system now supports separate main and secondary logos for print reports. You can upload different logos for the left and right sides of all print reports.

## How to Upload Logos

### Step 1: Access System Settings
1. Login to the admin panel
2. Navigate to **System Settings** (Admin → System Settings)

### Step 2: Upload Print Report Logos
1. Scroll down to the **"Print Report Logos"** section
2. You'll see two upload areas:
   - **Main Logo (Left Side)**: Upload the logo that appears on the left side of reports
   - **Secondary Logo (Right Side)**: Upload the logo that appears on the right side of reports

### Step 3: Upload Process
1. Click **"Choose main logo file"** for the left side logo
2. Select your logo file (supports JPG, PNG, SVG formats)
3. Click **"Choose secondary logo file"** for the right side logo
4. Select your second logo file
5. Click **"Update"** to save the changes

## Current Logo Configuration
Based on the test results, your system currently has:
- **Main Logo**: `uploads/1760056920_Logo.png.png`
- **Secondary Logo**: `uploads/1760056920_384549274_843563040829321_4297563294452634980_n.png`
- **Default Logo**: `uploads/1744257240_starhonda-removebg-preview.png` (fallback)

## Print Reports Affected
The following reports will use the uploaded logos:
- ✅ OR/CR Documents Management Report
- ✅ Invoice Management Report
- ✅ Orders Report
- ✅ Service Requests Report

## Logo Behavior
- **If main logo is uploaded**: It appears on the left side of reports
- **If secondary logo is uploaded**: It appears on the right side of reports
- **If either logo is missing**: The system falls back to the default logo
- **Logo positioning**: Left and right sides of the report header

## Technical Details
- **File Storage**: Logos are stored in the `uploads/` directory
- **Database**: Logo paths are stored in the `system_info` table
- **Fallback System**: If main/secondary logos aren't set, uses the default system logo
- **File Formats**: Supports JPG, PNG, SVG, and other image formats
- **Auto-resize**: Logos are automatically resized to fit the report layout

## Troubleshooting
- **Logo not appearing**: Check if the file was uploaded successfully
- **Wrong logo showing**: Verify the correct logo was selected for main/secondary
- **Logo too small/large**: The system automatically resizes logos for optimal display
- **File upload error**: Ensure the file is a valid image format and not too large

## Benefits
- **Flexible Branding**: Use different logos for different purposes
- **Professional Appearance**: Dual logos create a balanced, professional look
- **Easy Management**: Upload and change logos through the admin interface
- **Consistent Application**: All print reports automatically use the uploaded logos
