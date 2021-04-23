<?php
namespace DaData;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

/**
 * Class DaDataAbstractModel
 * @package DaData
 */
abstract class DaDataAbstractModel
{
    /**
     * @var ObjectId|null
     */
    public $_id = null;

    /**
     * @param string|ObjectId $id
     * @return $this|null
     */
    public static function getById($id)
    {
        if (!is_object($id)) {
            $id = new ObjectId($id);
        }

        $clz = get_called_class();
        $instance = new $clz();
        /* @var $instance DaDataAbstractModel */
        $document = $instance->getCollection()->findOne(
            [
                '_id' => $id
            ]
        );
        if (is_object($document) && $document instanceof BSONDocument) {
            return $instance->inflateModelFromDocument($document);
        }
        return null;
    }

    /**
     * @param array $query
     * @param array $options (optional)
     * @return $this|null
     */
    public static function getOneByQuery(array $query, array $options = [])
    {
        $clz = get_called_class();
        $instance = new $clz();
        /* @var $instance DaDataAbstractModel */
        $document = $instance->getCollection()->findOne(
            $query,
            $options
        );
        if (is_object($document) && $document instanceof BSONDocument) {
            return $instance->inflateModelFromDocument($document);
        }
        return null;
    }

    /**
     * @param array $query
     * @param array $options (optional)
     * @return $this[]|null
     */
    public static function getMultiByQuery(array $query, array $options = [])
    {
        $clz = get_called_class();
        $instance = new $clz();
        /* @var $instance DaDataAbstractModel */
        $resultSet = $instance->getCollection()->find(
            $query,
            $options
        );
        $documents = [];
        foreach ($resultSet as $item) {
            /* @var $item BSONDocument */
            $instance = new $clz();
            /* @var $instance DaDataAbstractModel */
            $documents[] = $instance->inflateModelFromDocument($item);
        }
        return $documents;
    }

    /**
     * @param BSONDocument $document
     * @return $this
     */
    protected function inflateModelFromDocument(BSONDocument $document)
    {
        $array = $document->getArrayCopy();
        foreach ($array as $k => $v) {
            $this->{$k} = $v;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $properties = get_class_vars(get_called_class());
        $toInsert = [];
        foreach ($properties as $k => $_) {
            $toInsert[$k] = $this->{$k};
        }
        unset($toInsert['_id']);
        if ($this->_id !== null) {
            // exsiting document, update
            return $this->getCollection()->updateOne(
                    [
                        '_id' => $this->_id
                    ],
                    [
                        '$set' => $toInsert
                    ]
                )->getMatchedCount() === 1;
        }

        // brand new insertion.
        $insert = $this->getCollection()->insertOne($toInsert);
        if ($insert->getInsertedCount() !== 1) {
            return false;
        }
        $this->_id = $insert->getInsertedId();
        return true;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (null === $this->_id) {
            return false;
        }
        return $this->getCollection()->deleteOne([
                '_id' => $this->_id
            ])->getDeletedCount() === 1;
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        $database = DaDataDatabaseStore::getInstance()->getDatabase();
        return $database->selectCollection(
            $this->getCollectionName()
        );
    }

    /**
     * @return string
     */
    abstract protected function getCollectionName();
}