# Scandiweb Junior Developer Test — Fullstack Monorepo

[🇺🇸 English Section](#english-version) | [🇧🇷 Seção em Português](#versão-em-português)

---

<a name="english-version"></a>
## 🇺🇸 English Version

This repository contains a fullstack **Product Management System** developed as a technical assessment. The project is split into two main layers, both following strict **Clean Architecture** and **Polymorphism** principles to handle different product types without conditional logic (`if/else` or `switch/case`).

### 📂 Repository Structure

* **`/api` (Backend):** A custom PHP micro-framework built from scratch (no external frameworks). It features a custom IoC Container, Query Builder, and Middleware Pipeline.
    * [Read API Documentation (English)](api/README.md)
    * [Leia a Documentação da API (Português)](api/README.pt-br.md)
* **`/client` (Frontend):** A Nuxt 4 SPA built with Vuetify 3 and Zod. It uses component-based polymorphism to manage dynamic forms.
    * [Read Client Documentation (English)](client/README.md)
    * [Leia a Documentação do Client (Português)](client/README.pt-br.md)

### 🚀 Quick Start (Docker)
*(Optional: If you have a docker-compose.yml, add the command here. If not, use the following:)*

1.  **Backend:** Navigate to `/api`, run `composer install` and follow the setup in its README.
2.  **Frontend:** Navigate to `/client`, run `npm install` and follow the setup in its README.

---

<a name="versão-em-português"></a>
## 🇧🇷 Versão em Português

Este repositório contém um **Sistema de Gestão de Produtos** completo, desenvolvido como um desafio técnico. O projeto é dividido em duas camadas principais, ambas seguindo princípios rigorosos de **Arquitetura Limpa** e **Polimorfismo** para lidar com diferentes tipos de produtos sem o uso de condicionais (`if/else` ou `switch/case`).

### 📂 Estrutura do Repositório

* **`/api` (Backend):** Um micro-framework PHP construído do zero (sem frameworks externos). Possui Container IoC próprio, Query Builder e Pipeline de Middlewares.
    * [Leia a Documentação da API (Português)](api/README.pt-br.md)
    * [Read API Documentation (English)](api/README.md)
* **`/client` (Frontend):** Uma SPA em Nuxt 4 construída com Vuetify 3 e Zod. Utiliza polimorfismo baseado em componentes para gerenciar formulários dinâmicos.
    * [Leia a Documentação do Client (Português)](client/README.pt-br.md)
    * [Read Client Documentation (English)](client/README.md)

### 🛠️ Diferenciais Técnicos
- **Zero Framework no Backend:** Demonstração de domínio profundo de PHP e padrões de projeto (SOLID, GoF).
- **Single Source of Truth no Frontend:** Validação e tipagem (TypeScript) derivadas inteiramente de Schemas Zod.
- **Polimorfismo em ambas as camadas:** Implementação escalável onde novos tipos de produtos podem ser adicionados apenas registrando novas classes/componentes, sem alterar a lógica existente.

---
**Author:** Giovanni Vasconcelos
