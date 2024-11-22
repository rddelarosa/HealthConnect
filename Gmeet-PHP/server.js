const express = require('express');
const { google } = require('googleapis');
const session = require('express-session');

const app = express();
const port = 3000;

// OAuth 2.0 setup
const CLIENT_ID = '840263782158-e64doljueubq615caj40ctpa4rle1orn.apps.googleusercontent.com';
const CLIENT_SECRET = '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';
const REDIRECT_URI = 'http://localhost:3000/oauth2callback';

const oauth2Client = new google.auth.OAuth2(
  CLIENT_ID,
  CLIENT_SECRET,
  REDIRECT_URI
);

// Middleware for session management
app.use(session({
  secret: 'your-session-secret',
  resave: false,
  saveUninitialized: true,
}));

// Serve static files (e.g., HTML, CSS, JS)
app.use(express.static('public'));

// Route to initiate the Google OAuth login
app.get('/auth', (req, res) => {
  const authUrl = oauth2Client.generateAuthUrl({
    access_type: 'offline',
    scope: ['https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email'],
  });
  res.redirect(authUrl);
});

// OAuth callback route to handle the Google login response
app.get('/oauth2callback', async (req, res) => {
  const { code } = req.query;
  
  try {
    const { tokens } = await oauth2Client.getToken(code);
    oauth2Client.setCredentials(tokens);

    // Save the user info into session
    const oauth2 = google.oauth2({
      auth: oauth2Client,
      version: 'v2',
    });
    const userInfo = await oauth2.userinfo.get();
    
    // Store user info in session
    req.session.user = userInfo.data;
    res.redirect('/booking');  // Redirect to booking page after login
  } catch (error) {
    console.error('Error during OAuth callback:', error);
    res.send('Error during authentication');
  }
});

// Booking route (only accessible after authentication)
app.get('/booking', (req, res) => {
  if (!req.session.user) {
    return res.redirect('/auth');  // Redirect to auth if user is not logged in
  }

  res.send(`
    <h1>Welcome, ${req.session.user.name}!</h1>
    <form action="/bookAppointment" method="POST">
      <label for="diagnostic_lab">Diagnostic Lab:</label>
      <select id="diagnostic_lab" name="diagnostic_lab">
        <option value="teleconsult">Teleconsult</option>
        <option value="face_to_face">Face to Face Consultation</option>
        <option value="CBC">Diagnostics - CBC</option>
        <option value="x-ray">Diagnostics - X-ray</option>
      </select>
      
      <label for="hospital">Hospital:</label>
      <select id="hospital" name="hospital_id"></select>

      <label for="doctor_name">Doctor:</label>
      <select id="doctor_name" name="doctor_id"></select>

      <label for="appointment_date">Appointment Date:</label>
      <input type="date" id="appointment_date" name="appointment_date">

      <label for="appointment_time">Appointment Time:</label>
      <input type="time" id="appointment_time" name="appointment_time">

      <label for="chief_complaint">Chief Complaint:</label>
      <input type="text" id="chief_complaint" name="chief_complaint">

      <button type="submit">Submit</button>
    </form>
  `);
});

// Booking form submission route
app.post('/bookAppointment', (req, res) => {
  // Here, process the booking form data and save it to the database
  const { username } = req.session.user; // Get username from session
  const { diagnostic_lab, hospital_id, doctor_id, appointment_date, appointment_time, chief_complaint } = req.body;
  
  // Logic to save to database here...

  res.send('Appointment successfully booked!');
});

// Start server
app.listen(port, () => {
  console.log(`Server is running at http://localhost:${port}`);
});
