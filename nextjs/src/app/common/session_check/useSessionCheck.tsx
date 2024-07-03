import { useState, useEffect } from 'react';

const useSessionCheck = () => {
    const [isLoading, setIsLoading] = useState(true);
    const [isAuthenticated, setIsAuthenticated] = useState(false);

    useEffect(() => {
        const checkSession = async () => {
            try {
                const response = await fetch(`${process.env.API_DOMAIN}/api/check_session`, {
                    method: 'GET',
                    credentials: 'include',
                });

                if (response.status === 200) {
                    setIsAuthenticated(true);
                } else {
                    setIsAuthenticated(false);
                    window.location.href = `/error/invalid_session`;
                }
            } catch (error) {
                console.error('Failed to check session:', error);
                alert('Failed to check session:' + error);
                setIsAuthenticated(false);
                window.location.href = `/login`;
            } finally {
                setIsLoading(false);
            }
        };
        checkSession();
    }, []);

    return { isLoading, isAuthenticated };
};

export default useSessionCheck;
