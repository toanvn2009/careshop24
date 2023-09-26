<?php
declare(strict_types=1);

namespace Careshop\RokanthemesBrand\ViewModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokanthemes\Brand\Model\ResourceModel\Brand\Collection;
use Rokanthemes\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;

/**
 * View model Brand
 */
class Brand implements ArgumentInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var BrandCollectionFactory
    */
    protected $brandCollectionFactory;

    public function __construct(
        ResourceConnection $resource,
        BrandCollectionFactory $brandCollectionFactory
    ) {
        $this->resource = $resource;
        $this->brandCollectionFactory = $brandCollectionFactory;
    }

    /**
     * @param array $brandIds
     *
     * @return Collection|null
     */
    public function getBrandCollection(array $brandIds): ?Collection
    {
        if (empty($brandIds)) {
            return null;
        }
        /** @var Collection $brandCollection */
        $brandCollection = $this->brandCollectionFactory->create();
        $brandCollection->addFieldToFilter('status', 1)
            ->addFieldToFilter('brand_id', ['in' => $brandIds])
            ->setOrder('position');
        return $brandCollection;
    }
}
