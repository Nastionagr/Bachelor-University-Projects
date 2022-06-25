package program_code;

import java.util.ArrayList;

public class AllElements {
    private ArrayList<Bill> bills = new ArrayList<Bill>(); 
    private ArrayList<Client> clients = new ArrayList<Client>(); 
    private ArrayList<Product> products = new ArrayList<Product>(); 

    public ArrayList<Bill> getBills() {
        return bills;
    }

    public void setBills(ArrayList<Bill> bills) {
        this.bills = bills;
    }

    public ArrayList<Client> getClients() {
        return clients;
    }

    public void setClients(ArrayList<Client> clients) {
        this.clients = clients;
    }

    public ArrayList<Product> getProducts() {
        return products;
    }

    public void setProducts(ArrayList<Product> products) {
        this.products = products;
    }
    
    public void addBill (Bill bill){
        this.bills.add(bill);
    }
    
    public void addClient (Client client){
        this.clients.add(client);
    }
    
    public void addProduct (Product product){
        this.products.add(product);
    }
    
    public void changeCname (Client client, String newName){
        for (int i = 0; i < this.clients.size(); i++) {
            if (this.clients.get(i) == client){
                this.clients.get(i).setName(newName);
                break;
            } 
        }
    }
    
    public void changeCcountry (Client client, String newCountry){
        for (int i = 0; i < this.clients.size(); i++) {
            if (this.clients.get(i) == client){
                this.clients.get(i).setCountry(newCountry);
                break;
            } 
        }
    }
    
    public void changeCcity (Client client, String newCity){
        for (int i = 0; i < this.clients.size(); i++) {
            if (this.clients.get(i) == client){
                this.clients.get(i).setCity(newCity);
                break;
            } 
        }
    }
    
    public void changeCaddress (Client client, String newAddress){
        for (int i = 0; i < this.clients.size(); i++) {
            if (this.clients.get(i) == client){
                this.clients.get(i).setAddress(newAddress);
                break;
            } 
        }
    }
    
    public void changeCpostcode (Client client, String newPostcode){
        for (int i = 0; i < this.clients.size(); i++) {
            if (this.clients.get(i) == client){
                this.clients.get(i).setPostcode(newPostcode);
                break;
            } 
        }
    }

    public void changePname (Product product, String newName){
        for (int i = 0; i < this.products.size(); i++) {
            if (this.products.get(i) == product){
                this.products.get(i).setName(newName);
                break;
            } 
        }
    }

    public void changePdetails (Product product, String newDetails){
        for (int i = 0; i < this.products.size(); i++) {
            if (this.products.get(i) == product){
                this.products.get(i).setDetails(newDetails);
                break;
            } 
        }
    }
    
    public void changePprice (Product product, Float newPrice){
        for (int i = 0; i < this.products.size(); i++) {
            if (this.products.get(i) == product){
                this.products.get(i).setPrice(newPrice);
                break;
            } 
        }
    }
}