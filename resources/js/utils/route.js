import { usePage } from '@inertiajs/vue3';

export function useRoute() {
    const $page = usePage();
    
    function route(name, params = {}, absolute = true) {
        try {
            const ziggy = $page?.props?.ziggy;
            if (!ziggy) {
                console.error('Ziggy is not properly initialized');
                return '#';
            }
            
            let url = ziggy.location + '/' + ziggy.url;
            
            // Remove any double slashes
            url = url.replace(/([^:]\/)\/+/g, '$1');
            
            // Find the route by name
            const routeObj = ziggy.routes?.[name];
            if (!routeObj) {
                console.error(`Route [${name}] not found.`);
                return '#';
            }
            
            // Replace route parameters
            let path = routeObj.uri || '';
            for (const [key, value] of Object.entries(params)) {
                path = path.replace(`{${key}}`, value).replace(`{${key}?}`, value);
            }
            
            // Remove any remaining optional parameters
            path = path.replace(/\{.*?\?\}/g, '');
            
            return absolute ? `${url}${path}` : path;
        } catch (error) {
            console.error('Error in route helper:', error);
            return '#';
        }
    }

    function currentRoute() {
        try {
            const path = window?.location?.pathname?.replace(/^\//, '') || '';
            const routes = $page?.props?.ziggy?.routes || {};
            
            const route = Object.entries(routes).find(([_, r]) => {
                if (!r?.uri) return false;
                // Convert route pattern to regex
                const pattern = r.uri
                    .replace(/\//g, '\\/')
                    .replace(/\{.*?\}/g, '[^/]+');
                const regex = new RegExp(`^${pattern}$`);
                return regex.test(path);
            });
            
            return route ? route[0] : '';
        } catch (error) {
            console.error('Error in currentRoute helper:', error);
            return '';
        }
    }

    return { route, currentRoute };
}

// Add to window for debugging
if (typeof window !== 'undefined') {
    window.useRoute = useRoute;
}
