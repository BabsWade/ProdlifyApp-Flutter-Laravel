import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _login() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      // Assurez-vous que l'URL est correcte
      var url = Uri.parse('http://10.0.2.2:8000/api/login'); 
      var response = await http.post(url, body: {
  

        'email': _emailController.text,
        'password': _passwordController.text,
      });
print("Status Code: ${response.statusCode}");
print("Response Body: ${response.body}");

      var data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['token'] != null) {
        // Stocker le token dans les SharedPreferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token']);

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Connexion réussie")),
        );

        // Naviguer vers la liste des produits après connexion réussie
        Navigator.pushReplacementNamed(context, '/products');
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(data['message'] ?? 'Erreur de connexion')),
        );
      }
print("Fin de la requête");

      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Connexion")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(labelText: 'Email'),
                validator: (value) =>
                    !value!.contains('@') ? 'Email invalide' : null,
              ),
              TextFormField(
                controller: _passwordController,
                obscureText: true,
                decoration: const InputDecoration(labelText: 'Mot de passe'),
                validator: (value) => value!.length < 8
                    ? 'Au moins 8 caractères'
                    : null,
              ),
              const SizedBox(height: 20),
              _isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : ElevatedButton(
                      onPressed: _login,
                      child: const Text("Se connecter"),
                    ),
              TextButton(
                onPressed: () {
                  Navigator.pushNamed(context, '/products'); // Pour la page d'inscription
                },
                child: const Text("Pas encore inscrit ? S'inscrire"),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
