import { BrowserRouter, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './auth/AuthContext'
import { Layout } from './components/Layout'
import { ProtectedRoute } from './components/ProtectedRoute'
import { LoginPage } from './pages/LoginPage'
import { AgentsPage } from './pages/AgentsPage'
import { NewAgentPage } from './pages/NewAgentPage'

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route element={<ProtectedRoute />}>
            <Route element={<Layout />}>
              <Route index element={<AgentsPage />} />
              <Route path="agents/new" element={<NewAgentPage />} />
            </Route>
          </Route>
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}

export default App
