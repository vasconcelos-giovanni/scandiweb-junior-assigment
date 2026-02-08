/**
 * Centralized API fetch wrapper.
 * Provides a configured `$fetch` instance with the base URL
 * from runtime configuration, used by all API composables.
 */
export default function useAPI() {
    const config = useRuntimeConfig()

    const baseFetch = $fetch.create({
        baseURL: `${config.public.apiBaseUrl}`,
    })

    async function fetch<T = unknown>(
        url: string,
        options?: Parameters<typeof baseFetch>[1],
    ): Promise<T> {
        return baseFetch(url, options) as T
    }

    return { fetch }
}
