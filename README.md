# Booking System – Project Summary

## ✅ What Has Been Completed

### Backend
- Built a backend server using **Symfony** and **PostgreSQL**.
- Designed and organized a **database schema** that meets the project requirements.

### API
- Successfully exposed **2 endpoints**:  
  - `GET /sessions`: Fetch available sessions  
  - `POST /bookings`: Create new bookings  
- Implemented logic to **prevent overlapping bookings**.

### Email Notification
- Enabled **email notification** upon successful booking.  
  - *(Currently facing a bug; root cause unknown, possibly related to mail server configuration)*

### Frontend
- Developed a **Vue.js web app** with the following pages:
  - **Session**: Browse available sessions by day.
  - **Cart**: Temporarily store selected sessions.
  - **Booking**: Finalize and submit bookings.

---

## 🚀 If Given More Time, I Would Improve

- Add **trainer management** to prevent overlapping schedules for the same trainer (currently not managed).
- Send emails **asynchronously** to improve booking API performance.
- Implement **user and booking management system** to support multi-user access and tracking.

---