library(tidyverse)
#library(magrittr)
#library(readr)
library(ggplot2)
library(gridExtra)
library(caret)

# Initialization of the data
setwd("C:/Users/Nastia/Desktop/university/DSP/Zadanie_1")
data <- read_csv("Drug_Consumption.csv")
View(data)

# Checking how many attributes and values do we have in our examination
nrow(data)
ncol(data)

# Checking the datatypes and mean value of the attributes
map_chr(data, typeof)
map_dbl(data, mean)

# Analyse age, education and national origin distribution of all participants
data %>% group_by(Age) %>% summarize(count=n())
data %>% group_by(Country) %>% summarize(count=n())
data %>% group_by(Education) %>% summarize(count=n())

# Remove the participants, who tried a fictitious Semeron drug
data <- data[grepl("CL0", data$Semer),]

# Delete all unnecessary columns from our data frame
useless_info <- c("Gender", "Education","Country","Ethnicity", 
                 "Nscore", "Escore", "Oscore", "AScore", "Cscore", "Impulsive", 
                 "Cannabis", "Nicotine", "Caff", "Choc", 
                 "Mushrooms", "Legalh", "Alcohol", "Semer")
data <- data[,!(names(data) %in% useless_info)]

# Decode the age of the participants to the numeric values for the future analysis
data$Age[data$Age == "18-24"] <- 0
data$Age[data$Age == "25-34"] <- 1
data$Age[data$Age == "35-44"] <- 2
data$Age[data$Age == "45-54"] <- 3
data$Age[data$Age == "55-64"] <- 4
data$Age[data$Age == "65+"] <- 5

hard_drugs <- c("Amphet", "Amyl", "Benzos", "Coke", "Crack", "Ecstasy", 
                "Heroin", "Ketamine", "LSD", "Meth", "VSA")

# Change categorical values to the numbers
data <- data %>% mutate_at(hard_drugs, ~ as.numeric(str_replace(., "CL", "")))

# Also changing the datatype in the Age column
data <- data %>% mutate_at("Age", ~ as.numeric(.))

# Divide all participants into 3 groups
# hard_drugs_never - people who never used to have any of hard drugs
# hard_drugs_active - people who do have hard drugs during the last period of time
# hard_drugs_sometimes - people who had drugs long time ago
hard_drugs_never <- data %>% filter(if_all(hard_drugs, ~ . == 0))
hard_drugs_active <- data %>% filter(if_any(hard_drugs, ~ . %in% c(4,5,6)))
hard_drugs_sometimes <- setdiff(data, union(hard_drugs_never,hard_drugs_active))

df <- list(data, hard_drugs_never, hard_drugs_sometimes, hard_drugs_active)

# Average SS values on each group
lapply(df, function(x) mean(x$SS))

# Counting the linear models for each data frame
# SS - target variable, Age - independent variable
# another way: lmodels <- lapply(df, function(x) lm(SS ~ Age, data = x))
lmodels <- df %>% map(~ lm(SS ~ Age, data = .x))

# Create the new sheet for the graphs
plot.new()
# Function that helps to fit the output graph
display <- function (x) {
    ggplot(x$model, aes_string(x = names(x$model)[2], y = names(x$model)[1])) + 
    geom_point() +
    stat_smooth(method = "lm", col = "cornsilk") +
    labs(title = "Linear Model",
         caption = paste(" Intercept = ", signif(x$coef[[1]], 5),
                         " Slope = ",     signif(x$coef[[2]], 5),
                         " P_value = ",   signif(summary(x)$coef[2,4], 5)))}

# Display the graphs
p1 <- display(lmodels[[1]]) 
p2 <- display(lmodels[[2]])
p3 <- display(lmodels[[3]])
p4 <- display(lmodels[[4]])
grid.arrange(p1, p2, p3, p4, nrow = 2, ncol = 2)

# Performing repeated K-fold cross-validation
# Generate the seed for the repeatable samples
set.seed(777)
# K = 10, repeating 3 times
train_method <- trainControl(method = "repeatedcv", number = 10, repeats = 3)
cv_models <- lapply(df, function(x) train(SS ~ Age, data = x, method = "lm", trControl = train_method))
