import { useEffect, useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'

function App() {
  const [suppliers, setSuppliers] = useState([]);
  const [products, setProducts] = useState([]);
  const [product, setProduct] = useState([]);
  const [supplier, setSupplier] = useState([]);
  const [total, setTotal] = useState(0);

  const fetchSuppliers = async () => {
    const response = await fetch("http://localhost/purchaseorder/supplier.php");
    const data = await response.json();
    setSuppliers(data);
  };

  const fetchProducts = async () => {
    const response = await fetch("http://localhost/purchaseorder/product.php");
    const data = await response.json();
    setProducts(data);
  };

  const fetchSingleProduct = async (id) => {
    const response = await fetch("http://localhost/purchaseorder/product.php?id="+id);
    const data = await response.json();
    const isDuplicate = Array.isArray(product) && product.some((product) => product.id == id);
    if(!isDuplicate) {
      let price = roundToTwo(data[0].price);
      let gst = roundToTwo((price * 1) * 0.18);
      let item_price = roundToTwo(price + gst);
      setProduct([...product, { id: data[0].id, code: data[0].code, product_name: data[0].product_name, size: data[0].size, price: price, qty: 1, gst: gst, item_price: item_price }]);
    }
  };

  const roundToTwo = (num) => {
      return +(Math.round(num + "e+2")  + "e-2");
  }

  const handleQtyChange = (event, index) => {
    const { value } = event.target;
  
    setProduct((prevState) => 
      prevState.map((item, i) =>
        i === index
          ? {
              ...item,
              qty: value,
              item_price: roundToTwo((item.price * value) + (item.price * value * 0.18)),
              gst: roundToTwo(item.price * value * 0.18),
            }
          : item
      )
    );
  };

  const handleSupplierChange = (event) => {
    const value = event.target.value;
    setSupplier(value);
  }

  const handleProductChange = (event) => {
    const value = event.target.value;
    fetchSingleProduct(value);
  }

  const handleSave = async () => {
    try {
      const formData = new FormData();
      formData.append("supplier", supplier);
      formData.append("products", JSON.stringify(product));
      formData.append("total", total);
      
      const response = await fetch("http://localhost/purchaseorder/save.php", {
        method: "POST",
        body: formData,
      });
      const newProduct = await response.json();
      
      if (newProduct.error) {
        alert(newProduct.error);
      } else {
        window.open("http://localhost/purchaseorder/view.php?order="+newProduct.order_id);
      }
    } catch (error) {
      
    }
  };

  useEffect(()=> {
    fetchSuppliers();
    fetchProducts();
  }, []);

  useEffect(() => {
    const totalPrice = product.reduce((total, product) => total + product.item_price, 0);
    setTotal(totalPrice);
  }, [product]);

  return (
    <>
      <table>
        <thead>
        <tr>
          <td colSpan={4}><select id='supplier_data' onChange={handleSupplierChange}><option value="">Select Supplier</option>
          { Array.isArray(suppliers) && suppliers.map((supplier) => (<option key={supplier.id} value={supplier.id}>{supplier.name}</option>))}
          </select></td>
          <td colSpan={3}><select id='product_data' onChange={handleProductChange}><option value="">Select Product</option>
          { Array.isArray(products) && products.map((product) => (<option key={product.id} value={product.id}>{product.product_name}</option>))}
          </select></td>
        </tr>
        <tr>
          <th className='header'>Code</th>
          <th className='header'>Product Name</th>
          <th className='header'>Size</th>
          <th className='header'>Qty</th>
          <th className='header'>Cost Price</th>
          <th className='header'>GST (18%)</th>
          <th className='header'>Total</th>
        </tr>
        </thead>
        <tbody>
        {product.map((product, index) => (
          <tr key={index}>
            <td>{product.code}</td>
            <td>{product.product_name}</td>
            <td>{product.size}</td>
            <td><input type="number" name="qty[]" onChange={(event) => handleQtyChange(event, index)} value={product.qty}/></td>
            <td>{product.price}</td>
            <td>{product.gst}</td>
            <td>{product.item_price}</td>
          </tr>
          ))}
        </tbody>
        <tfoot>
          <tr>
            <td colSpan={5}></td>
            <td className='header' align='right'>Total</td>
            <td>{roundToTwo(total)}</td>
          </tr>
          <tr>
            <td colSpan={6}></td>
            <td><input type="button" onClick={handleSave} value="Submit & View Invoice" /></td>
          </tr>
        </tfoot>
      </table>
    </>
  )
}

export default App
