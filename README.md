<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

---

# Meter Readings Dashboard

This project is a Laravel-based web application for managing meter readings. It allows users to upload and manage meters and their readings while providing additional functionality such as estimated readings and bulk uploads. 

---

## Features

- **Meter Management**:
  - Add meters with unique identifiers (MPXN), installation dates, and types (electricity/gas).
  - View a list of meters and their details, including readings.
  
- **Meter Readings**:
  - Record new meter readings with validation to ensure valid integer inputs.
  - Display readings with associated meter details.

- **Estimated Readings**:
  - Calculate readings based on previous data and estimated annual consumption (EAC).
  - Validation ensures readings fall within acceptable ranges.
  
- **Bulk Upload**:
  - Upload multiple meter readings in CSV format.
  - Validate and process uploads, with invalid rows identified.

---

## Development Process and Tools

### Leveraging GitHub Copilot
GitHub Copilot assisted in:
- Writing comments and documentation for better code clarity.
- Generating meaningful unit tests using PHPUnit.

#### Unit Testing
- `MeterControllerTest`: Tests CRUD operations for meters.
- `MeterReadingControllerTest`: Validates adding and estimating meter readings.
- `ProcessBulkUploadTest`: Ensures proper processing of bulk uploads.

---

## Note on Exercise B.2

Due to time constraints, Exercise B.2, involving backend processing for bulk uploads with workers, was not completed. However:
- Bulk upload logic (Exercise B.1) is implemented.
- Form and validation for uploads are functional, laying groundwork for future worker-based enhancements.

---

## Contributing

Contributions are welcome! Please follow [Laravel's contribution guide](https://laravel.com/docs/10.x/contributions) for details.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
