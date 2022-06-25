package program_code;
import java.util.ArrayList;
import java.util.Date;

public class Bill {
    private Date currentDate;
    private Client client;
    private ArrayList<Product> products;
    private ArrayList<Integer> quantity;

    public Bill(Client client, ArrayList<Product> products, ArrayList<Integer> quantity, Date currentDate) {
        this.currentDate = currentDate;
        this.client = client;
        this.products = products;
        this.quantity = quantity;
    }

    public Date getCurrentDate() {
        return currentDate;
    }

    public void setCurrentDate(Date currentDate) {
        this.currentDate = currentDate;
    }

    public Client getClient() {
        return client;
    }

    public void setClient(Client client) {
        this.client = client;
    }

    public ArrayList<Product> getProducts() {
        return products;
    }

    public ArrayList<Integer> getQuantity() {
        return quantity;
    }

    public void setQuantity(ArrayList<Integer> quantity) {
        this.quantity = quantity;
    }
    
    public void setProducts(ArrayList<Product> products) {
        this.products = products;
    }
    
    public String toString(){
        return this.client.getName() + " (" + this.client.getCountry() + "): " + String.valueOf(this.currentDate);
    }
}
