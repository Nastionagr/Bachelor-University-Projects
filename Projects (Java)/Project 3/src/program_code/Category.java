package program_code;
import java.io.Serializable;

public class Category implements Serializable {
    private String name, description, currency;
    private Float pricePerDay;

    public Category(String name, String description, String currency, Float pricePerDay) {
        this.name = name;
        this.description = description;
        this.currency = currency;
        this.pricePerDay = pricePerDay;
    }

    public String getCurrency() {
        return currency;
    }

    public void setCurrency(String currency) {
        this.currency = currency;
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

    public Float getPricePerDay() {
        return pricePerDay;
    }

    public void setPricePerDay(Float pricePerDay) {
        this.pricePerDay = pricePerDay;
    }
    
    public String toString()
    {
         return this.name + "  -  " + String.valueOf(this.pricePerDay) + " " + this.currency;
    }
}