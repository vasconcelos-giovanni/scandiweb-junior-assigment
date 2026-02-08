<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\HttpStatus;
use App\Core\Response;
use App\Exceptions\DuplicateSkuException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

class ExceptionHandlerMiddleware implements MiddlewareInterface
{
    public function handle(\Closure $next)
    {
        try {
            return $next();
        } catch (NotFoundException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (DuplicateSkuException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        } catch (ValidationException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
