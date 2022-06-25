package program_code;
import java.io.Serializable;
import java.util.ArrayList;

public class Room implements Serializable {
    private String number;
    private int discount, condition;
    private Category category;

    public Room(String number, Category category, int discount, int condition) {
        this.number = number;
        this.discount = discount;
        this.condition = condition;
        this.category = category;
    }

    public String getNumber() {
        return number;
    }

    public void setNumber(String number) {
        this.number = number;
    }

    public int getDiscount() {
        return discount;
    }

    public void setDiscount(int discount) {
        this.discount = discount;
    }

    public int getCondition() {
        return condition;
    }

    public void setCondition(int condition) {
        this.condition = condition;
    }

    public Category getCategory() {
        return category;
    }

    public void setCategory(Category category) {
        this.category = category;
    }
    
    public String toString()
    {    
        String information = "room № " + this.number + " - ";
        information += String.valueOf(this.category.getPricePerDay()) + " " + this.category.getCurrency();
        
        if(this.discount == 0)
        {information += "  (without any discounts)";}
        else
        {information += "  (" + String.valueOf(this.discount) + "% OFF if you stay more than " + String.valueOf(this.condition) + " day(s))";}
        
        return information;
    }
    
    public ArrayList<Reservation> seeAllReservations(ArrayList<Reservation> reservations)
    {
        ArrayList<Reservation> history = new ArrayList<Reservation>();
        
        for(Reservation r : reservations){
            if(this.equals(r.getRoom()))
                {history.add(r);}}
        
        return history;
    }
}