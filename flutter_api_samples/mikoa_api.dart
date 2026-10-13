import 'dart:convert';
import 'package:http/http.dart' as http;

/// Base URL of your API (change to your server)
const String baseUrl = 'https://yourdomain.com';

/// Response model for mikoa API
class MikoaResponse {
  final bool success;
  final bool error;
  final String message;
  final List<Mkoa> mikoa;

  MikoaResponse({
    required this.success,
    required this.error,
    required this.message,
    required this.mikoa,
  });

  factory MikoaResponse.fromJson(Map<String, dynamic> json) {
    return MikoaResponse(
      success: json['success'] ?? false,
      error: json['error'] ?? true,
      message: json['message'] ?? '',
      mikoa: (json['mikoa'] as List<dynamic>?)
              ?.map((e) => Mkoa.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

class Mkoa {
  final int id;
  final int? companyId;
  final String regionalName;

  Mkoa({
    required this.id,
    this.companyId,
    required this.regionalName,
  });

  factory Mkoa.fromJson(Map<String, dynamic> json) {
    return Mkoa(
      id: json['id'] as int,
      companyId: json['company_id'] as int?,
      regionalName: json['regional_name'] as String? ?? '',
    );
  }
}

/// Fetches mikoa (regions) by company_id or user_id.
///
/// [companyId] – optional; filter by company.
/// [userId] – optional; company is derived from this user.
/// At least one of [companyId] or [userId] must be provided (or use auth).
///
/// Returns [MikoaResponse] on success.
/// Throws on network error; check [MikoaResponse.success] and [MikoaResponse.error] for API errors.
Future<MikoaResponse> fetchMikoa({
  int? companyId,
  int? userId,
  String? token,
}) async {
  final queryParams = <String, String>{};
  if (companyId != null) queryParams['company_id'] = companyId.toString();
  if (userId != null) queryParams['user_id'] = userId.toString();

  final uri = Uri.parse('$baseUrl/api/mikoa/index').replace(queryParameters: queryParams.isNotEmpty ? queryParams : null);

  final headers = <String, String>{
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };
  if (token != null && token.isNotEmpty) {
    headers['Authorization'] = 'Bearer $token';
  }

  final response = await http.get(uri, headers: headers);

  if (response.statusCode != 200) {
    try {
      final err = jsonDecode(response.body) as Map<String, dynamic>;
      return MikoaResponse(
        success: err['success'] ?? false,
        error: true,
        message: err['message'] ?? 'Request failed',
        mikoa: [],
      );
    } catch (_) {
      throw Exception('Request failed: ${response.statusCode} ${response.body}');
    }
  }

  final data = jsonDecode(response.body) as Map<String, dynamic>;
  return MikoaResponse.fromJson(data);
}

/// Example usage in a widget or service:
///
/// ```dart
/// try {
///   final res = await fetchMikoa(companyId: 1);
///   if (res.success && !res.error) {
///     for (final m in res.mikoa) {
///       print('${m.id}: ${m.regionalName}');
///     }
///   } else {
///     print('API error: ${res.message}');
///   }
/// } catch (e) {
///   print('Network error: $e');
/// }
/// ```
