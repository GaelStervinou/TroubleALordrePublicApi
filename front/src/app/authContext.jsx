import { createContext, useContext, useState, useEffect } from 'react';
import SetUpInstance from '../utils/axios.js';
import { API_LOGIN_ROUTE, API_ME_ROUTE, API_USERS_ROUTE } from '../utils/apiRoutes.js';
import { eraseCredentials, storeCredentials, localStorageStoreItem, localStorageGetItem } from '../utils/localStorage.js';
import { getUserCompanies } from '../queries/companies.js';


const http = SetUpInstance();

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [status, setStatus] = useState('idle');
  const [error, setError] = useState([]);

  const login = async (loginData) => {
    setStatus('loading');
    try {
      const request = await http.post(API_LOGIN_ROUTE, {
        email: loginData.email,
        password: loginData.password,
      });
      const data = await request.data;
      setStatus('succeeded');
      storeCredentials(data.token, data.refresh_token);
      await getMe();
    } catch (error) {
      setStatus('failed');
      setError(error.response.data);
      setUser(null);
      eraseCredentials();
      throw error;
    }
  };

  const getMe = async () => {
    setStatus('loading');
    try {
      if (!localStorageGetItem('talopToken')){
        setStatus('failed');
        return;
      }
      
      const request = await http.get(API_ME_ROUTE);
      const data = await request.data;
      setStatus('succeeded');
      
      setUser({
        id: data.id,
        firstname: data.firstname,
        lastname: data.lastname,
        email: data.email,
        roles: data.roles,
        status: data.status,
        kbis: data.kbis,
      });
  
      localStorageStoreItem('user', data, true);
    } catch (e) {
      setStatus('failed');
      setError(e.response.data);
      eraseCredentials();
    }
  };

  const logout = () => {
    setUser(null);
    setStatus('loggedOut');
    eraseCredentials();
  };

  const register = async (registerData) => {
    try {
      setStatus('loading');
      const request = await http.post(API_USERS_ROUTE, {
        email: registerData.email,
        plainPassword: registerData.password,
        verifyPassword: registerData.verifyPassword,
        firstname: registerData.firstname,
        lastname: registerData.lastname,
      });
      const data = await request.data;
      setStatus('succeeded');
      localStorageStoreItem('user', data, true);
    } catch (e) {
      setStatus('failed');
      setError(e.response.data);
      setUser(null);
      eraseCredentials();
    }
  }

  const goToMyProfile = () => {
    window.location.replace('/profile/'.concat(user.id));
  }

  const isMyProfile = (userId) => {
    const user = retrieveUser(); 
    if (!user) {
      return false;
    }

    return user.id === userId;
  }

  const isCompanyAdmin = () => {
    const user = retrieveUser(); 
    if (!user) {
      return false;
    }

    return user.roles.includes('ROLE_COMPANY_ADMIN');
  }

  const isAdmin = () => {
    const user = retrieveUser(); 
    if (!user) {
      return false;
    }

    return user.roles.includes('ROLE_ADMIN');
  }

  const isTroubleMaker = () => {
    const user = retrieveUser(); 
    if (!user) {
      return false;
    }

    return user.roles.includes('ROLE_TROUBLE_MAKER');
  }

  const isLoggedIn = () => {
    if (localStorageGetItem('talopToken') && localStorageGetItem('talopRefreshToken') ) {
      return true;
    }
    
    return false;
  }

  const retrieveUser = () => {
    if (localStorageGetItem('user')) {
      return JSON.parse(localStorageGetItem('user'));
    }

    return null;
  }

  const isMyCompany = async (companyId) => {
    const user = retrieveUser(); 
    if (!user) {
      return false;
    }

    const companies = await getUserCompanies(user.id);
    if (!companies) {
      return false;
    }

    return companies['hydra:member'].some((company) => company.id === companyId);
  }

  useEffect(() => {
    const checkLoggedInUser = async () => {
      try {
        setStatus('loading');
        await getMe();
        setStatus('succeeded');
      } catch (e) {
        setStatus('failed');
        setError(e.response?.data);
        eraseCredentials();
      }

      if (status === 'succeeded' && (window.location.pathname === '/login' || window.location.pathname === '/profile' || window.location.pathname === '/register')) {
        goToMyProfile()
      }
    };

    checkLoggedInUser();
  }, []);

  const value = { user, status, login, logout, getMe, register, goToMyProfile, isMyProfile, isCompanyAdmin, isAdmin, isTroubleMaker, isLoggedIn, retrieveUser, isMyCompany };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  return useContext(AuthContext);
};
