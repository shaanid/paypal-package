# Professional PayPal Integration Pro

A production-standard Laravel implementation of PayPal's REST API integration, featuring robust error handling, transaction persistence, and audit logging.

## 🚀 Features

- **Service-Oriented Architecture**: Decoupled payment logic in `PayPalService`.
- **Database Persistence**: Automatic tracking of every transaction (Pending, Completed, Canceled, Failed).
- **Audit Logging**: Full traceability of API requests and responses in `storage/logs/laravel.log`.
- **Input Validation**: Secure handling of payment data via specialized `FormRequest`.
- **Premium UI**: Modern, responsive checkout interface with glassmorphism aesthetics.

## 🛠️ Setup Instructions

1.  **Environment Configuration**:
    Add your PayPal credentials to your `.env` file:

    ```env
    PAYPAL_MODE=sandbox
    PAYPAL_SANDBOX_CLIENT_ID=your_client_id
    PAYPAL_SANDBOX_CLIENT_SECRET=your_client_secret
    PAYPAL_CURRENCY=USD
    ```

2.  **Run Migrations**:

    ```bash
    php artisan migrate
    ```

3.  **Local Testing**:
    Start the development server:
    ```bash
    php artisan serve
    ```
    Navigate to `http://localhost:8000/paypal` to access the checkout page.

## 📁 Architecture Overview (Enterprise Pattern)

This project follows the **Action Pattern**, decoupling business logic from controllers for maximum testability and reusability.

- **`app/DTOs/PayPal/PaymentData.php`**: Data Transfer Object for type-safe payment information.
- **`app/Actions/PayPal/`**:
    - `CreatePayPalOrderAction`: Handles record initialization and PayPal order creation.
    - `CompletePayPalPaymentAction`: Handles payment capture and status finalization.
    - `CancelPayPalPaymentAction`: Handles user-initiated cancellations.
- **`app/Services/PayPal/PayPalService.php`**: Low-level infrastructure service wrapping the PayPal SDK.
- **`app/Http/Controllers/PayPal/PayPalController.php`**: ultra-thin orchestrator that injects and executes actions.
- **`app/Models/Transaction.php`**: Single source of truth for payment audits.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
