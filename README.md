# Booking System – Project Summary

## ✅ What Has Been Completed

### Backend
- Built a backend server using **Symfony** and **PostgreSQL**.
- Designed and structured a **database schema** that meets project requirements.

### API
- Exposed **2 main endpoints**:
  - `GET /sessions`: Fetch available sessions.
  - `POST /bookings`: Create a new booking.
- Implemented logic to **prevent overlapping bookings** based on time slot comparison.

### Email Notification
- Enabled **email notifications** on successful booking.  
  - *(Currently encountering a bug — possibly related to mail server issues.)*

### Frontend
- Built a **Vue.js web application** with 3 main pages:
  - **Session**: Browse sessions by date and availability.
  - **Cart**: Store selected sessions before booking.
  - **Booking**: Confirm and complete the booking process.

---

## ⚠ Known Issues

- **Timezone inconsistencies**:  
  Dates and times are not yet standardized across the system, leading to mismatches due to timezone differences.  
  A consistent timezone handling strategy (e.g. UTC storage + local display) should be applied.

---

## 🚀 If Given More Time, I Would Improve

- Add **trainer management**: Currently, trainers are not managed, which can result in double-bookings for the same trainer.
- Make **email sending asynchronous** to enhance booking API performance and user experience.
- Implement **user accounts and booking history**, enabling authentication and personalized session tracking.

---