/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
      extend: {
        fontFamily: {
          sans: ['Vazirmatn', 'system-ui', '-apple-system', 'sans-serif'],
          mono: ['Fira Code', 'monospace'],
        },
        colors: {
          primary: {
            50: '#eff6ff',
            100: '#dbeafe',
            200: '#bfdbfe',
            300: '#93c5fd',
            400: '#60a5fa',
            500: '#3b82f6',
            600: '#2563eb',
            700: '#1d4ed8',
            800: '#1e40af',
            900: '#1e3a8a',
          },
          secondary: {
            50: '#f8fafc',
            100: '#f1f5f9',
            200: '#e2e8f0',
            300: '#cbd5e1',
            400: '#94a3b8',
            500: '#64748b',
            600: '#475569',
            700: '#334155',
            800: '#1e293b',
            900: '#0f172a',
          },
          success: {
            500: '#10b981',
          },
          warning: {
            500: '#f59e0b',
          },
          danger: {
            500: '#ef4444',
          },
          info: {
            500: '#3b82f6',
          },
        },
        borderRadius: {
          'sm': '0.5rem',
          'md': '1rem',
          'lg': '1.5rem',
          'xl': '2rem',
          'full': '9999px',
        },
        animation: {
          'float': 'float 6s ease-in-out infinite',
          'shimmer': 'shimmer 2s infinite',
          'gradient-shift': 'gradient-shift 3s ease infinite',
          'pulse-glow': 'pulse-glow 2s infinite',
          'slide-up': 'slide-up 0.6s ease-out',
          'fade-in': 'fade-in 0.5s ease-out',
          'spin': 'spin 1s linear infinite',
          'blob': 'blob 7s infinite',
        },
        keyframes: {
          blob: {
            "0%": {
              transform: "translate(0px, 0px) scale(1)",
            },
            "33%": {
              transform: "translate(30px, -50px) scale(1.1)",
            },
            "66%": {
              transform: "translate(-20px, 20px) scale(0.9)",
            },
            "100%": {
              transform: "translate(0px, 0px) scale(1)",
            },
          },
          float: {
            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
            '50%': { transform: 'translateY(-20px) rotate(5deg)' },
          },
          shimmer: {
            '0%': { backgroundPosition: '-200% center' },
            '100%': { backgroundPosition: '200% center' },
          },
          'gradient-shift': {
            '0%': { backgroundPosition: '0% 50%' },
            '50%': { backgroundPosition: '100% 50%' },
            '100%': { backgroundPosition: '0% 50%' },
          },
          'pulse-glow': {
            '0%, 100%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.3)' },
            '50%': { boxShadow: '0 0 40px rgba(59, 130, 246, 0.6)' },
          },
          'slide-up': {
            'from': { opacity: '0', transform: 'translateY(30px)' },
            'to': { opacity: '1', transform: 'translateY(0)' },
          },
          'fade-in': {
            'from': { opacity: '0' },
            'to': { opacity: '1' },
          },
        },
        backdropBlur: {
          xs: '2px',
          sm: '4px',
          md: '8px',
          lg: '12px',
          xl: '16px',
          '2xl': '24px',
          '3xl': '32px',
        },
        boxShadow: {
          'soft': '0 10px 40px -10px rgba(0, 0, 0, 0.1)',
          'medium': '0 20px 60px -15px rgba(0, 0, 0, 0.15)',
          'hard': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
          'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
          'inner': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
        },
        backgroundImage: {
          'gradient-primary': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
          'gradient-secondary': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
          'gradient-dark': 'linear-gradient(135deg, #0f172a 0%, #000 100%)',
          'gradient-success': 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
        },
        transitionProperty: {
          'smooth': 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
          'bounce': 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)',
          'elastic': 'all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)',
        },
      },
    },
    plugins: [
      require('@tailwindcss/forms'),
      require('@tailwindcss/typography'),
      require('@tailwindcss/aspect-ratio'),
    ],
  }