import axios from '@/configs/axios';
import Cookies from 'js-cookie';

class AuthService {
  async login(payload) {
    try {
      const response = await axios.post('/auth/login', payload);

      if (response.status !== 200) {
        return {
          success: false,
          messages: response.messages
        };
      }

      const token = response.data.access_token;
      sessionStorage.setItem('role', response.data.catalogue);
      // set token to Cookie
      Cookies.set('token', token, {
        expires: parseInt(import.meta.env.VITE_REFRESHTOKEN_EXPIRES)
      });
      return {
        success: true,
        data: { token, catalogue: response.data.catalogue },
        messages: response.messages
      };
    } catch (error) {
      return {
        success: false,
        messages: error.response.data.messages
      };
    }
  }
  async loginOtp(payload) {
    try {
      const response = await axios.post('/auth/login/otp', payload);

      if (response.status !== 200) {
        return {
          success: false,
          messages: response.messages
        };
      }

      const token = response.data.access_token;
      // set token to Cookie
      Cookies.set('token', token, {
        expires: parseInt(import.meta.env.VITE_REFRESHTOKEN_EXPIRES)
      });
      return {
        success: true,
        data: { token, catalogue: response.data.catalogue },
        messages: response.messages
      };
    } catch (error) {
      return {
        success: false,
        messages: error.response.data.messages
      };
    }
  }
  async refreshToken() {
    try {
      const response = await axios.post('/auth/refreshToken');

      if (response.status !== 200) {
        return {
          success: false,
          messages: response.messages
        };
      }

      const token = response.data.access_token;
      // set token to Cookie
      Cookies.set('token', token, {
        expires: parseInt(import.meta.env.VITE_REFRESHTOKEN_EXPIRES)
      });

      return {
        success: true,
        data: token
      };
    } catch (error) {
      return {
        success: false,
        messages: [error.message]
      };
    }
  }

  async me() {
    try {
      const response = await axios.get('/auth/me');
      sessionStorage.setItem('role', response.catalogue_code);

      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        messages: [error.message]
      };
    }
  }

  async register($payload) {
    try {
      const response = await axios.post('/auth/register', $payload);

      return {
        success: true,
        messages: response.messages
      };
    } catch (error) {
      return {
        success: false,
        messages: error.response.data.messages
      };
    }
  }
  async forgot($payload) {
    try {
      const response = await axios.post('/auth/forgot-password', $payload);

      return {
        success: true,
        messages: response.messages
      };
    } catch (error) {
      return {
        success: false,
        messages: error.response.data.messages
      };
    }
  }

  async logout() {
    try {
      const response = await axios.post('/auth/logout');
      sessionStorage.removeItem('role');

      return {
        success: true,
        messages: response.messages
      };
    } catch (error) {
      return {
        success: false,
        messages: error.response.data.messages
      };
    }
  }

  async googleLogin($payload) {
    try {
      const response = await axios.post('/auth/google/callback', $payload);

      if (response.status !== 200) {
        return {
          success: false,
          messages: response.messages
        };
      }

      const token = response.data.access_token;
      // set token to Cookie
      Cookies.set('token', token, {
        expires: parseInt(import.meta.env.VITE_REFRESHTOKEN_EXPIRES)
      });

      return {
        success: true,
        data: token
      };
    } catch (error) {
      return {
        success: false,
        messages: [error.message]
      };
    }
  }
}

export default new AuthService();
