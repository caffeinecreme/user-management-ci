# User Management System - CodeIgniter 3

A simple user management system built with CodeIgniter 3, integrating DataTables, AJAX, and FakeStore API.

## Features

- View list of users with server-side pagination
- Add new users with form validation
- Edit existing user data
- Delete users with confirmation
- AJAX operations without page refresh
- Responsive interface with Bootstrap

## Technologies

- **Framework**: CodeIgniter 3
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Data Communication**: AJAX
- **API Integration**: FakeStore API

## Installation

1. Clone or download this repository
2. Place it in your web server directory (htdocs, www, html)
3. Set the base URL in `application/config/config.php`:

   ```php
   // Option 1: Standard localhost
   $config['base_url'] = 'http://localhost/user_management/';

   // Option 2: Using custom port (e.g., 8001)
   // $config['base_url'] = 'http://localhost:8001/user_management/';
   ```

4. Ensure the `application/cache` and `application/logs` folders have proper permissions
5. Access the application through your browser:
   - Standard port: `http://localhost/user_management/`
   - Custom port: `http://localhost:8001/user_management/`

## Project Structure

```
user_management/
├── application/
│   ├── controllers/
│   │   ├── Users.php
│   ├── models/
│   │   ├── User_model.php
│   ├── views/
│   │   ├── templates/
│   │   ├── users/
│   ├── helpers/
│   │   ├── api_helper.php
├── assets/
│   ├── js/
│   ├── css/
```

## Note

This application uses the FakeStore API as its data source. This API does not actually persist data changes but simulates successful responses.

## License

Open source, free to use and modify as needed.
