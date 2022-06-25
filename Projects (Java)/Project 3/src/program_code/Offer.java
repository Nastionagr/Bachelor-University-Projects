package program_code;
import java.io.Serializable;

public class Offer implements Serializable{
    private String name, description, currency;
    private Float price;

    public Offer(String name, String description, String currency, Float price) {
        this.name = name;
        this.description = description;
        this.currency = currency;
        this.price = price;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getCurrency() {
        return currency;
    }

    public void setCurrency(String currency) {
        this.currency = currency;
    }

    public Float getPrice() {
        return price;
    }

    public void setPrice(Float price) {
        this.price = price;
    }
    
    public String toString()
    {
         return this.name + "  -  " + String.valueOf(this.price) + " " + this.currency;
    }
}