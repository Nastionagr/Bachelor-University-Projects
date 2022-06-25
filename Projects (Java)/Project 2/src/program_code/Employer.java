package program_code;
import java.io.File;

public class Employer {
    private String name, sphere;
    private File logo;
    private int numberOfWorkers;

    public Employer(String name, String sphere, File logo) {
        this.name = name;
        this.sphere = sphere;
        this.logo = logo;
        this.numberOfWorkers = 0;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getSphere() {
        return sphere;
    }

    public void setSphere(String sphere) {
        this.sphere = sphere;
    }

    public File getLogo() {
        return logo;
    }

    public void setLogo(File logo) {
        this.logo = logo;
    }

    public int getNumberOfWorkers() {
        return numberOfWorkers;
    }

    public void setNumberOfWorkers(int numberOfWorkers) {
        this.numberOfWorkers = numberOfWorkers;
    }
    
    public String toString(){
        return this.name + " - " + this.sphere;
    }
}