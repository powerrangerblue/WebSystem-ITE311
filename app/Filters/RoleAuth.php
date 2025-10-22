<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the session
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        
        // Get user role and current URI
        $userRole = strtolower($session->get('role'));
        $currentUri = $request->getUri()->getPath();
        
        // Define access permissions based on role
        $hasAccess = false;
        
        // Define basic routes that all logged-in users can access
        $basicRoutes = ['/', '/about', '/contact', '/logout'];
        
        // Check if current URI is a basic route
        if (in_array($currentUri, $basicRoutes)) {
            $hasAccess = true;
        } else {
            // Check role-specific access
            switch ($userRole) {
                case 'admin':
                    // Admin can access any route starting with /admin
                    if (strpos($currentUri, '/admin') === 0) {
                        $hasAccess = true;
                    }
                    break;

                case 'teacher':
                    // Teacher can access routes starting with /teacher
                    if (strpos($currentUri, '/teacher') === 0) {
                        $hasAccess = true;
                    }
                    break;
                    
                case 'student':
                    // Student can access routes starting with /student and /announcements
                    if (strpos($currentUri, '/student') === 0 || $currentUri === '/announcements') {
                        $hasAccess = true;
                    }
                    break;
            }
        }
        
        // If user doesn't have access, redirect with error message
        if (!$hasAccess) {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            
            // Redirect based on user role to their appropriate dashboard
            switch ($userRole) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                    break;
                case 'teacher':
                    return redirect()->to('/teacher/dashboard');
                    break;
                case 'student':
                default:
                    return redirect()->to('announcements');
                    break;
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
