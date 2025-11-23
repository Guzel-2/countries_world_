<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Model\Country;
use App\Model\CountryScenarios;

use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\DuplicatedCodeException;
use App\Model\Exceptions\InvalidCountryException;
use App\Model\Exceptions\DuplicatedCountryException;

#[Route(path: 'api/country', name: 'app_api_country')]
final class CountryController extends AbstractController
{
    public function __construct(
        private readonly CountryScenarios $countries
    ) {
    }

    // получение всех стран
    #[Route(path: '', name: 'app_api_country_root', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        try {
            $countriesPreview = [];
            foreach ($this->countries->getAll() as $country) {
                $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
                array_push($countriesPreview, $countryPreview);
            }
            return $this->json(data: $countriesPreview, status: 200);
        } catch (InvalidCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (\Exception $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 500);
            return $response;
        }
    }

    // получение страны по коду
    #[Route(path: '/{code}', name: 'app_api_country_code', methods: ['GET'])]
    public function get(string $code): JsonResponse
    {
        try {
            $country = $this->countries->get($code);
         
            $data = [
                'shortName' => $country->getShortName(),
                'fullName' => $country->getFullName(),
                'isoAlpha2' => $country->getIsoAlpha2(),
                'isoAlpha3' => $country->getIsoAlpha3(),
                'isoNumeric' => $country->getIsoNumeric(),
                'population' => $country->getPopulation(),
                'square' => $country->getSquare(),
            ];
            return $this->json(data: $data);
        } catch (InvalidCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (CountryNotFoundException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        } catch (InvalidCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (\Exception $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 500);
            return $response;
        }
    }

    // добавление страны
    #[Route(path: '', name: 'app_api_country_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
       
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                throw new InvalidCountryException('Недопустимые JSON-данные');
            }
        
            $requiredFields = ['shortName', 'fullName', 'isoAlpha2', 'isoAlpha3', 'isoNumeric', 'population', 'square'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || $data[$field] === null) {
                    throw new InvalidCountryException("Отсутствует или null поле: {$field}");
                }
            }

            $data['isoAlpha2'] = strtoupper($data['isoAlpha2']);
            $data['isoAlpha3'] = strtoupper($data['isoAlpha3']);

     
            $validationErrors = [];
            if (strlen($data['isoAlpha2']) !== 2 || !ctype_alpha($data['isoAlpha2'])) {
                $validationErrors[] = ['field' => 'isoAlpha2', 'message' => 'Должно быть ровно 2 заглавными буквами'];
            }
            if (strlen($data['isoAlpha3']) !== 3 || !ctype_alpha($data['isoAlpha3'])) {
                $validationErrors[] = ['field' => 'isoAlpha3', 'message' => 'Должно быть ровно 3 заглавными буквами'];
            }
            if (!is_numeric($data['isoNumeric']) || strlen($data['isoNumeric']) !== 3 || (int)$data['isoNumeric'] < 0) {
                $validationErrors[] = ['field' => 'isoNumeric', 'message' => 'Должно быть 3-значным неотрицательным числом'];
            }
            if (!is_numeric($data['population']) || (int)$data['population'] < 0) {
                $validationErrors[] = ['field' => 'population', 'message' => 'Должно быть неотрицательным числом'];
            }
            if (!is_numeric($data['square']) || (int)$data['square'] < 0) {
                $validationErrors[] = ['field' => 'square', 'message' => 'Должно быть неотрицательным числом'];
            }
            if (empty($data['shortName']) || strlen($data['shortName']) > 255) {
                $validationErrors[] = ['field' => 'shortName', 'message' => 'Должно быть строкой длиной 1-255 символов'];
            }
            if (empty($data['fullName']) || strlen($data['fullName']) > 255) {
                $validationErrors[] = ['field' => 'fullName', 'message' => 'Должно быть строкой длиной 1-255 символов'];
            }

            if (!empty($validationErrors)) {
                throw new InvalidCountryException('Недопустимые данные для добавления страны', $validationErrors);
            }

         
            $country = new Country($data);
            $this->countries->store($country);
            $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
            return $this->json(data: $countryPreview, status: 200);
        } catch (InvalidCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (InvalidCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (\Exception $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 500);
            return $response;
        }
    }

    // редактирование страны
    #[Route(path: '/{code}', name: 'app_api_country_edit', methods: ['PATCH'])]
    public function edit(Request $request, string $code): JsonResponse
    {
        try {
            // Проверка валидности кода 
            if (empty($code) || strlen($code) !== 2) {
                throw new InvalidCodeException('Недопустимый формат кода: должен быть 2 символа (ISO Alpha2)');
            }

            // Получить текущие данные страны
            $existingCountry = $this->countries->get($code);

        
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                throw new InvalidCountryException('Недопустимые JSON-данные');
            }

        
            $shortName = $data['shortName'] ?? $existingCountry->getShortName();
            $fullName = $data['fullName'] ?? $existingCountry->getFullName();
            $isoAlpha2 = isset($data['isoAlpha2']) ? strtoupper($data['isoAlpha2']) : $existingCountry->getIsoAlpha2();
            $isoAlpha3 = isset($data['isoAlpha3']) ? strtoupper($data['isoAlpha3']) : $existingCountry->getIsoAlpha3();
           $isoNumeric = isset($data['isoNumeric']) ? str_pad((string)$data['isoNumeric'], 3, '0', STR_PAD_LEFT) : $existingCountry->getIsoNumeric();
            $population = isset($data['population']) ? (int) $data['population'] : $existingCountry->getPopulation();
            $square = isset($data['square']) ? (int) $data['square'] : $existingCountry->getSquare();

            // Строгая валидация обновленных данных с накоплением ошибок 
            $validationErrors = [];
            if (isset($data['isoAlpha2']) && (strlen($isoAlpha2) !== 2 || !ctype_alpha($isoAlpha2))) {
                $validationErrors[] = ['field' => 'isoAlpha2', 'message' => 'Должно быть ровно 2 заглавными буквами'];
            }
            if (isset($data['isoAlpha3']) && (strlen($isoAlpha3) !== 3 || !ctype_alpha($isoAlpha3))) {
                $validationErrors[] = ['field' => 'isoAlpha3', 'message' => 'Должно быть ровно 3 заглавными буквами'];
            }
            if (isset($data['isoNumeric']) && (!is_numeric($data['isoNumeric']) || strlen((string) $isoNumeric) !== 3 || $isoNumeric < 0)) {
                $validationErrors[] = ['field' => 'isoNumeric', 'message' => 'Должно быть 3-значным неотрицательным числом'];
            }
            if (isset($data['population']) && (!is_numeric($data['population']) || $population < 0)) {
                $validationErrors[] = ['field' => 'population', 'message' => 'Должно быть неотрицательным числом'];
            }
            if (isset($data['square']) && (!is_numeric($data['square']) || $square < 0)) {
                $validationErrors[] = ['field' => 'square', 'message' => 'Должно быть неотрицательным числом'];
            }
            if (isset($data['shortName']) && (empty($shortName) || strlen($shortName) > 255)) {
                $validationErrors[] = ['field' => 'shortName', 'message' => 'Должно быть строкой длиной 1-255 символов'];
            }
            if (isset($data['fullName']) && (empty($fullName) || strlen($fullName) > 255)) {
                $validationErrors[] = ['field' => 'fullName', 'message' => 'Должно быть строкой длиной 1-255 символов'];
            }
            if (!empty($validationErrors)) {
                throw new InvalidCountryException('Недопустимые данные для редактирования страны', $validationErrors);
            }

            // Создание обновленного объекта Country
            $updatedData = [
                'shortName' => $shortName,
                'fullName' => $fullName,
                'isoAlpha2' => $isoAlpha2,
                'isoAlpha3' => $isoAlpha3,
                'isoNumeric' => $isoNumeric,
                'population' => $population,
                'square' => $square,
            ];
            $updatedCountry = new Country($updatedData);


            $this->countries->edit($code, $updatedCountry);

      
            $countryPreview = $this->buildCountryPreview(country: $updatedCountry, request: $request);
            return $this->json(data: $countryPreview, status: 200);

        } catch (InvalidCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (CountryNotFoundException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        } catch (DuplicatedCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (InvalidCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (\Exception $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 500);
            return $response;
        }
    }

    // удаление страны
    #[Route(path: '/{code}', name: 'app_api_country_remove', methods: ['DELETE'])]
    public function remove(string $code): JsonResponse
    {
        try {
            // Проверка валидности кода 
            if (empty($code) || strlen($code) !== 2) {
                throw new InvalidCodeException('Недопустимый формат кода: должен быть 2 символа (ISO Alpha2)');
            }

            $this->countries->delete($code);
            return $this->json(data: null, status: 204); 
        } catch (InvalidCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (CountryNotFoundException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        } catch (InvalidCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        } catch (\Exception $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 500);
            return $response;
        }
    }

   
    private function buildErrorResponse(Exception $ex): JsonResponse
    {
        $data = [
            'errorCode' => $ex->getCode(),
            'errorMessage' => $ex->getMessage(),
        ];

        if ($ex instanceof InvalidCountryException && !empty($ex->getErrors())) {
            $data['errors'] = $ex->getErrors();  // [{'field': 'isoAlpha2', 'message': '...'}]
        }

        return $this->json(data: $data);
    }


    private function buildCountryPreview(Country $country, Request $request): array
    {
        $uri = sprintf(
            '%s://%s/api/country/%s',
            $request->getScheme(),
            $request->getHttpHost(),
            $country->getIsoAlpha2(),
        );
        return [
            'shortName' => $country->getShortName(),
            'uri' => $uri,
        ];
    }
}
