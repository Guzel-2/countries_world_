<?php

namespace App\Rdb;

use mysqli;
use RuntimeException;
use Exception;

use App\Model\Country;
use App\Model\CountryRepository;
use App\Rdb\SqlHelper;

// RdbCountryStorage - имплементация хранилища стран, работающая с БД
class CountryStorage implements CountryRepository {

    private SqlHelper $sqlHelper;

    public function __construct(SqlHelper $sqlHelper) {
        $this->sqlHelper = $sqlHelper;
    }

    public function selectAll(): array {
        $countries = [];
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();  
            $query = $connection->query("SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country");
            if ($query) {
                while ($row = $query->fetch_assoc()) {
                    $countries[] = new Country([
                        'shortName' => $row['shortname'],
                        'fullName' => $row['fullname'],
                        'isoAlpha2' => $row['isoalpha2'],
                        'isoAlpha3' => $row['isoalpha3'],
                        'isoNumeric' => $row['isonumeric'],
                        'population' => $row['population'],
                        'square' => $row['square']
                    ]);
                }
                $query->close();  
            } else {
                throw new \Exception("Query failed: " . $connection->error);
            }
        } catch (\Exception $e) {
        
            error_log($e->getMessage());
       
        } finally {
          
        }
        return $countries;
    }

    public function selectByCode(string $code): ?Country {
        $country = null;
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country WHERE isoalpha2 = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $code);
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $country = new Country([
                    'shortName' => $row['shortname'],
                    'fullName' => $row['fullname'],
                    'isoAlpha2' => $row['isoalpha2'],
                    'isoAlpha3' => $row['isoalpha3'],
                    'isoNumeric' => $row['isonumeric'],
                    'population' => (int)$row['population'],
                    'square' => (int)$row['square']
                ]);
            }
        } finally {
          
        }
        return $country;
    }

  
    public function get(string $code): ?Country {
        return $this->selectByCode($code);
    }

    public function selectByAlpha2(string $alpha2): ?Country {
        return $this->selectByCode($alpha2);  
    }

    public function selectByAlpha3(string $alpha3): ?Country {
        $country = null;
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country WHERE isoalpha3 = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $alpha3);
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $country = new Country([
                    'shortName' => $row['shortname'],
                    'fullName' => $row['fullname'],
                    'isoAlpha2' => $row['isoalpha2'],
                    'isoAlpha3' => $row['isoalpha3'],
                    'isoNumeric' => $row['isonumeric'],
                    'population' => (int)$row['population'],
                    'square' => (int)$row['square']
                ]);
            }
        } finally {
         
        }
        return $country;
    }

    public function selectByNumeric(string $numeric): ?Country {
        $country = null;
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country WHERE isonumeric = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $numeric); 
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $country = new Country([
                    'shortName' => $row['shortname'],
                    'fullName' => $row['fullname'],
                    'isoAlpha2' => $row['isoalpha2'],
                    'isoAlpha3' => $row['isoalpha3'],
                    'isoNumeric' => $row['isonumeric'],
                    'population' => (int)$row['population'],
                    'square' => (int)$row['square']
                ]);
            }
        } finally {
     
        }
        return $country;
    }

    public function selectByShortName(string $shortName): ?Country {
        $country = null;
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country WHERE shortname = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $shortName);
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $country = new Country([
                    'shortName' => $row['shortname'],
                    'fullName' => $row['fullname'],
                    'isoAlpha2' => $row['isoalpha2'],
                    'isoAlpha3' => $row['isoalpha3'],
                    'isoNumeric' => $row['isonumeric'],
                    'population' => (int)$row['population'],
                    'square' => (int)$row['square']
                ]);
            }
        } finally {
  
        }
        return $country;
    }

    public function selectByFullName(string $fullName): ?Country {
        $country = null;
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'SELECT shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square FROM country WHERE fullname = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $fullName);
            $query->execute();
            $result = $query->get_result();
            if ($row = $result->fetch_assoc()) {
                $country = new Country([
                    'shortName' => $row['shortname'],
                    'fullName' => $row['fullname'],
                    'isoAlpha2' => $row['isoalpha2'],
                    'isoAlpha3' => $row['isoalpha3'],
                    'isoNumeric' => $row['isonumeric'],
                    'population' => (int)$row['population'],
                    'square' => (int)$row['square']
                ]);
            }
        } finally {
      
        }
        return $country;
    }

    public function save(Country $country): void {
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'INSERT INTO country (shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square)
                VALUES (?, ?, ?, ?, ?, ?, ?);';
            $query = $connection->prepare($queryStr);
           
            $shortName = $country->getShortName();
            $fullName = $country->getFullName();
            $isoAlpha2 = $country->getIsoAlpha2();
            $isoAlpha3 = $country->getIsoAlpha3();
            $isoNumeric = $country->getIsoNumeric();
            $population = $country->getPopulation();
            $square = $country->getSquare();
            $query->bind_param(
                'ssssiii', 
                $shortName,
                $fullName,
                $isoAlpha2,
                $isoAlpha3,
                $isoNumeric,
                $population,
                $square
            );
            if (!$query->execute()) {
                throw new Exception('insert execute failed');
            }
        } finally {
       
        }
    }

    public function delete(string $code): void {
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            $queryStr = 'DELETE FROM country WHERE isoalpha2 = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $code);
            if (!$query->execute()) {
                throw new Exception('delete execute failed');
            }
        } finally {
        
        }
    }

    public function update(string $code, Country $country): void {
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
           
            $queryStr = 'UPDATE country SET 
                    shortname = ?, 
                    fullname = ?,
                    population = ?, 
                    square = ?
                WHERE isoalpha2 = ?';
            $query = $connection->prepare($queryStr);
           
            $shortName = $country->getShortName();
            $fullName = $country->getFullName();
            $population = $country->getPopulation();
            $square = $country->getSquare();
            $query->bind_param(
                'ssiis',  
                $shortName,
                $fullName,
                $population,
                $square,
                $code  
            );
            if (!$query->execute()) {
                throw new Exception('update execute failed');
            }
        } finally {
 
        }
    }

    public function pingDb(): bool {
        $connection = null;
        try {
            $connection = $this->sqlHelper->getConnection();
            return $connection->ping();
        } catch (Exception $e) {
            return false;
        } finally {
         
        }
    }
}
