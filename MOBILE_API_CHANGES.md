# Mobile API Update Documentation

This document outlines the necessary updates for the mobile (Flutter) application to support the new Driver Management and Authentication workflow.

## 1. Driver Registration (Creation)

When creating a new driver from the app, the endpoint now accepts an `email` field and returns a generated `driver_id`.

**Endpoint:** `POST /api/management/driver_store` (or the mapped route for `ManagementController@driver_store`)

### Request Payload
- `name` (String, Required): Full name of the driver.
- `phone` (String, Optional): Phone number of the driver.
- `email` (String, Optional): Email address of the driver.
- `user_id` (Integer, Required): The ID of the user performing the action (used to determine `company_id`).

### Response Example (Success)
```json
{
  "success": true,
  "error": false,
  "message": "New Driver registered successful. Driver ID: 123456",
  "drivers_id": 12
}
```
*Note: The 6-digit `driver_id` is generated on the server for login purposes. The `drivers_id` field contains the actual database ID of the driver.*

---

## 2. Authentication (Login)

The login API has been updated to support both traditional email-based login and name-based login for drivers.

**Endpoint:** `POST /api/login` (Mapped to `Auth_ApiController@login`)

### Login Methods
1.  **Staff/Admin:** Continue using `email` and `password`.
2.  **Drivers:** Use their **Name** (as `username` or `email` field) and their **6-digit Driver ID** as the `password`.

### Request Payload
- `username` (String, Required): Driver's Name or Staff Email.
- `password` (String, Required): 6-digit Driver ID or User Password.

### Updated Login Response
The `user` object in the response now includes the `is_driver` field.

```json
{
  "success": true,
  "error": false,
  "message": "User login successfully",
  "user": {
    "id": 12,
    "name": "John Driver",
    "email": "123456@emasuite.com",
    "is_driver": 1, 
    "drivers_id": 12, // New Field: Database ID of the driver (only if is_driver is 1)
    "company_id": 1,
    "user_type": "driver",
    "phone": "0712345678"
    // ... other user fields
  }
}
```

---

## 3. Required Changes in Flutter App

### A. Data Models
Update your `User` model to include the `is_driver` field:
```dart
class User {
  final int id;
  final String name;
  final String email;
  final int isDriver; // New Field: 0 = Staff, 1 = Driver
  // ... other fields
}
```

### B. Login Screen Logic
Update the login screen to handle the redirection based on the user type:
```dart
void handleLoginResponse(Map<String, dynamic> response) {
  if (response['success']) {
    var user = response['user'];
    if (user['is_driver'] == 1) {
      // Redirect to Driver-specific Dashboard
      Navigator.pushReplacementNamed(context, '/driver_dashboard');
    } else {
      // Redirect to Standard Dashboard
      Navigator.pushReplacementNamed(context, '/standard_dashboard');
    }
  }
}
```

### C. Driver Creation Form
Add an **Email** field to the driver registration form. Inform the user that the generated **Driver ID** will be used as their password once the administrator approves their account from the web dashboard.

---

## 4. Payment Update (Received Money Only)

A new endpoint has been added to update the received money (`ela_iliyopokelewa`) for a parcel without dropping any quantity.

**Endpoint:** `POST /api/drop_off_point/update_parcel_payment` (Mapped to `ManagementController@update_parcel_payment`)

### Request Payload
- `customer_pacel_id` (Integer, Required): The ID of the parcel.
- `drop_off_point_id` (Integer, Required): The ID of the drop-off point.
- `driver_route_id` (Integer, Required): The ID of the current driver route.
- `amount_received` (Numeric, Required): The amount of money received now.
- `user_id` (Integer, Optional): The ID of the user performing the update (defaults to auth user).

### Response Example (Success)
```json
{
    "success": true,
    "error": false,
    "message": "Payment updated successfully",
    "pacel_history": { ... }, // The created history record
    "jumla_kuu": 150000.0,    // Total cost of the parcel
    "ela_iliyopokelewa": 50000.0, // New total received amount
    "hela_iliyobaki": 100000.0   // Remaining balance
}
```

### Required Changes in Flutter App

#### Flutter Function Example
Add this function to your API service to handle payment updates:

```dart
Future<Map<String, dynamic>> updateParcelPayment({
  required int customerParcelId,
  required int dropOffPointId,
  required int driverRouteId,
  required double amountReceived,
  int? userId,
  String? token,
}) async {
  final url = Uri.parse('$baseUrl/api/drop_off_point/update_parcel_payment');
  
  final body = jsonEncode({
    'customer_pacel_id': customerParcelId,
    'drop_off_point_id': dropOffPointId,
    'driver_route_id': driverRouteId,
    'amount_received': amountReceived,
    if (userId != null) 'user_id': userId,
  });

  final headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  };

  final response = await http.post(url, headers: headers, body: body);

  return jsonDecode(response.body);
}
```

### 5. Correcting History Data and IDs

The `drop_off_point/parcels_history` endpoint has been updated to include the correct parcel ID and transaction-specific amounts.

**Endpoint:** `GET /api/drop_off_point/parcels_history?driver_route_id=XX`

#### Updated Response Fields in `history` array:
- `customer_pacel_id` (New): Use this ID when calling `update_parcel_payment` or `drop_parcel`. Do **NOT** use the top-level `id` (which is the drop-off record ID).
- `amount_received`: Now correctly reflects the amount paid **during that specific transaction** (from `drop_off_parcels` table).
- `ela_iliyopokelewa`: Reflects the **cumulative total** paid for that parcel up to that point.

#### Flutter Change Needed:
Ensure your model and UI use `customer_pacel_id` for payment updates:

```dart
// When clicking an item in history to update payment:
onTap: () {
  updateParcelPayment(
    customerParcelId: historyItem['customer_pacel_id'], // USE THIS, NOT 'id'
    dropOffPointId: historyItem['drop_off_point_id'],
    driverRouteId: historyItem['driver_route_id'],
    amountReceived: newAmount,
  );
}
```
