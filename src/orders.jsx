import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './Order.css'
import App from './Order.jsx'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)
